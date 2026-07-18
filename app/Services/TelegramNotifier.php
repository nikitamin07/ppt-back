<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/** Шлёт текстовые уведомления в Telegram всем чатам из config('services.telegram'). */
final class TelegramNotifier
{
    public function send(string $text): void
    {
        $bot = config('services.telegram.bot');
        $chats = array_filter(array_map('trim', explode(',', (string) config('services.telegram.chats'))));

        if (! $bot || $chats === []) {
            return;
        }

        foreach ($chats as $chatId) {
            try {
                Http::asForm()
                    ->timeout(5)
                    ->post("https://api.telegram.org/{$bot}/sendMessage", [
                        'chat_id' => $chatId,
                        'text' => $text,
                    ])
                    ->throw();
            } catch (Throwable $e) {
                // Не даём сбою Telegram уронить ответ формы — заявка уже сохранена в БД.
                Log::warning('Telegram notify failed', ['chat_id' => $chatId, 'error' => $e->getMessage()]);
            }
        }
    }
}
