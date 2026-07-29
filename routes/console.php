<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Тик чаще паузы между письмами, темп задаёт mailing.interval_seconds
Schedule::command('mailing:dispatch')->everyFifteenSeconds()->withoutOverlapping();
