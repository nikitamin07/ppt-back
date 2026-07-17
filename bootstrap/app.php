<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Приложение живёт за nginx-прокси: схему (http/https) и IP клиента
        // берём из X-Forwarded-* заголовков, которые проставляет nginx.
        $middleware->trustProxies(at: '*');

        // Кириллица в ответах API — без \uXXXX-экранирования
        $middleware->api(append: [
            \App\Http\Middleware\UnescapedJsonResponse::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
