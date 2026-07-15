<?php

// API только читается браузером с фронтенда: разрешаем GET с доверенных Origin.
// В проде фронт и /api живут на одном домене за nginx (CORS не задействуется),
// заголовки нужны для dev-режима (next dev на :3000) и на случай разнесения доменов.
return [

    'paths' => ['api/*'],

    'allowed_methods' => ['GET'],

    'allowed_origins' => array_filter(explode(',', (string) env(
        'CORS_ALLOWED_ORIGINS',
        'http://localhost:3000,https://xn--o1aaj.xn--90ais',
    ))),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 3600,

    'supports_credentials' => false,

];
