<?php

use App\Http\Middleware\LegacyRedirects;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Редиректы со старых адресов проверяются до маршрутизации.
        $middleware->prepend(LegacyRedirects::class);
        $middleware->alias(['locale' => SetLocale::class, 'page-cache' => \App\Http\Middleware\CachePages::class]);
        // Cookie выбранного языка читается как есть (его ставит JS переключателя).
        $middleware->encryptCookies(except: ['tenir_lang']);
        // За Cloudflare / прокси.
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
