<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MailingDelivery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/** Приём событий доставки от ESP */
final class MailingWebhookController extends Controller
{
    /** Событие ESP → статус доставки */
    private const STATUS_MAP = [
        'delivered' => 'delivered',
        // отскоки
        'hard_bounce' => 'bounced',
        'soft_bounce' => 'bounced',
        'bounce' => 'bounced',
        'block' => 'bounced',
        'blocked' => 'bounced',
        'invalid_email' => 'bounced',
        // жалобы на спам
        'spam' => 'complained',
        'spam_complaint' => 'complained',
        'complaint' => 'complained',
        'mark_spam' => 'complained',
    ];

    public function __invoke(Request $request, string $secret): Response|JsonResponse
    {
        $expected = (string) config('mailing.webhook_secret');
        abort_if($expected === '' || ! hash_equals($expected, $secret), 404);

        // Учёт выключен — принимаем молча
        if (! config('mailing.track_delivery')) {
            return response()->noContent();
        }

        foreach ($this->events($request) as $event) {
            $this->apply($event);
        }

        return response()->noContent();
    }

    /** @return array<int, array<string, mixed>> объект или массив событий */
    private function events(Request $request): array
    {
        $data = $request->json()->all();
        if ($data === []) {
            return [];
        }

        return array_is_list($data) ? $data : [$data];
    }

    /** @param array<string, mixed> $event */
    private function apply(array $event): void
    {
        $type = strtolower((string) ($event['event'] ?? ''));
        $status = self::STATUS_MAP[$type] ?? null;
        $email = (string) ($event['email'] ?? '');
        // Снимаем угловые скобки Message-ID
        $messageId = trim((string) ($event['message-id'] ?? $event['message_id'] ?? ''), " <>");

        if ($status === null || ($email === '' && $messageId === '')) {
            return;
        }

        $delivery = MailingDelivery::query()
            ->when($messageId !== '', fn ($q) => $q->where('provider_message_id', $messageId))
            ->when($messageId === '', fn ($q) => $q->where('email', $email)->whereIn('status', ['sent', 'bounced', 'delivered']))
            ->latest('sent_at')
            ->first();

        // Жалобу не перетираем — финальный статус
        if ($delivery && $delivery->status !== 'complained') {
            $delivery->update(['status' => $status]);
        }
    }
}
