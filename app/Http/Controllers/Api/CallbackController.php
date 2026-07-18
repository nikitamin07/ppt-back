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

    /** Форма «Заказать звонок» (order-callback на фронте). Тело валидируется, ответ пустой. */
    public function store(Request $request): Response
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:32'],
            'comment' => ['nullable', 'string', 'max:2000'],
            'source_url' => ['nullable', 'string', 'max:2048'],
        ]);

        CallbackRequest::create($data);

        $this->telegram->send($this->formatMessage($data));

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
