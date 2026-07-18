<?php

// API читается и частично пишется браузером с фронтенда: разрешаем GET+POST с доверенных Origin.
// В проде фронт и /api живут на одном домене за nginx (CORS не задействуется),
// заголовки нужны для dev-режима (next dev на :3000) и на случай разнесения доменов.
return [

    'paths' => ['api/*'],

    // POST — фильтрация каталога (POST /api/products/filter) и форма обратного звонка (POST /api/callback)
    'allowed_methods' => ['GET', 'POST'],

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
