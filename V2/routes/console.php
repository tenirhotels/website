<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Подарочные сертификаты из gifter: раз в 10 минут (нужен cron `php artisan schedule:run` каждую минуту).
Illuminate\Support\Facades\Schedule::command('site:sync-gifts')->everyTenMinutes()->withoutOverlapping();
