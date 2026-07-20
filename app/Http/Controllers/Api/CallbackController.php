<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CallbackRequest;
use App\Services\TelegramNotifier;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

final class CallbackController extends Controller
{
    public function __construct(private readonly TelegramNotifier $telegram) {}

    /** Заявки с одного номера повторно в этом окне молча игнорируются (не 422 — фронт не должен это знать). */
    private const DUPLICATE_WINDOW_MINUTES = 10;

    /** Форма «Заказать звонок» (order-callback на фронте). Тело валидируется, ответ пустой. */
    public function store(Request $request): Response
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            // Тот же формат, что шлёт фронт (phone-mask.ts): +375 и ровно 9 цифр.
            'phone' => ['required', 'string', 'regex:/^\+375\d{9}$/'],
            'comment' => ['nullable', 'string', 'max:2000'],
            // Рендерится в админке как кликабельная ссылка (CallbackRequestsTable) — только http(s),
            // иначе клиент мог бы подсунуть javascript:... и получить XSS в сессии админа.
            'source_url' => ['nullable', 'url:http,https', 'max:2048'],
        ]);

        $isDuplicate = CallbackRequest::where('phone', $data['phone'])
            ->where('created_at', '>=', now()->subMinutes(self::DUPLICATE_WINDOW_MINUTES))
            ->exists();

        if (! $isDuplicate) {
            CallbackRequest::create($data);

            $this->telegram->send($this->formatMessage($data));
        }

        return response()->noContent();
    }

    /** @param array{name: string, phone: string, comment?: string|null, source_url?: string|null} $data */
    private function formatMessage(array $data): string
    {
        $lines = [
            'НОВАЯ ЗАЯВКА НА ОБРАТНЫЙ ЗВОНОК!',
            "Имя: {$data['name']}",
            "Телефон: {$data['phone']}",
        ];

        if (! empty($data['comment'])) {
            $lines[] = "Комментарий: {$data['comment']}";
        }

        if (! empty($data['source_url'])) {
            $lines[] = "Страница: {$data['source_url']}";
        }

        return implode("\n", $lines);
    }
}
