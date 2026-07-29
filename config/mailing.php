<?php

return [

    // Пауза между письмами, секунды
    'interval_seconds' => (int) env('MAILING_INTERVAL_SECONDS', 75),

    // Потолок за сутки, 0 = без лимита
    'daily_limit' => (int) env('MAILING_DAILY_LIMIT', 0),

    'timezone' => env('MAILING_TIMEZONE', 'Europe/Minsk'),

    // Окно отправки [start, end)
    'window' => [
        'start' => (int) env('MAILING_WINDOW_START', 9),
        'end' => (int) env('MAILING_WINDOW_END', 17),
    ],

    // Статусы доставки через webhook ESP
    'track_delivery' => (bool) env('MAILING_TRACK_DELIVERY', false),

    'webhook_secret' => env('MAILING_WEBHOOK_SECRET'),

];
