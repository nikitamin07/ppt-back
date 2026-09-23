<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // без обёртки {"data": ...}
        JsonResource::withoutWrapping();

        // SSR фронтенда теперь с токеном, он вне лимита 60 запросов\сек
        RateLimiter::for('site-api', function (Request $request) {
            $token = (string) config('services.internal_api.token');
            $internal = $token !== '' && hash_equals($token, (string) $request->header('X-Internal-Token'));

            return $internal ? Limit::none() : Limit::perMinute(60)->by($request->ip());
        });
    }
}
