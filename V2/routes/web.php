<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ExperienceController;
use App\Http\Controllers\GiftController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SingleHotelController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\StoryController;
use App\Support\Locales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Публичному сайту не нужны сессии, CSRF и cookie — страницы можно кэшировать (и на CDN тоже).
$publicWithoutSession = [
    \Illuminate\Cookie\Middleware\EncryptCookies::class,
    \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
    \Illuminate\Session\Middleware\StartSession::class,
    \Illuminate\View\Middleware\ShareErrorsFromSession::class,
    \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
];

/*
|--------------------------------------------------------------------------
| Карта сайта Tenir Hotels
|--------------------------------------------------------------------------
| Два режима (App\Support\Property), переключаются автоматически:
|
| ОДИН ОТЕЛЬ (опубликован один отель) — сайт этого отеля:
| /{locale}                                     обзор отеля
| /{locale}/rooms[/{room}]                      номера
| /{locale}/dining · /gallery · /getting-here   ресторан, галерея, как добраться
| /{locale}/experiences · /offers               впечатления и предложения отеля
|
| СЕТЬ (опубликовано 2+ отеля) — сайт бренда:
| /{locale}                                     витрина бренда
| /{locale}/destinations                        все отели и «скоро открытие»
| /{locale}/destinations/{hotel}                обзор отеля
| /{locale}/destinations/{hotel}/rooms[/{room}] номера
| /{locale}/destinations/{hotel}/dining         рестораны и бары
| /{locale}/destinations/{hotel}/experiences    впечатления отеля
| /{locale}/destinations/{hotel}/offers         предложения отеля
| /{locale}/destinations/{hotel}/gallery        галерея
| /{locale}/destinations/{hotel}/getting-here   как добраться, FAQ
| /{locale}/experiences[/{slug}]                впечатления всей сети
| /{locale}/events[/{slug}]                     события курорта и отелей (календарь)
| /{locale}/offers[/{slug}]                     спецпредложения
| /{locale}/stories[/{slug}]                    истории и новости
| /{locale}/booking                             модуль бронирования Exely
| /{locale}/gifts                               подарочные сертификаты (список из gifter)
| /{locale}/map                                 интерактивная карта курорта (зима/лето)
| /{locale}/contact                             контакты
| /{locale}/{page}                              страницы бренда: about, sustainability, ...
*/

// Корень: язык из cookie (если посетитель уже выбирал), иначе по языку браузера, иначе казахский.
Route::get('/', function (Request $request) {
    $saved = $request->cookies->get('tenir_lang');
    $locale = Locales::isSupported($saved) ? $saved : Locales::fromBrowser($request->header('Accept-Language'));

    return redirect('/'.$locale)->header('Vary', 'Accept-Language, Cookie')->header('Cache-Control', 'private, no-store');
})->withoutMiddleware($publicWithoutSession);

// Адрес вида /lang/en/booking?date=… — так модуль Exely (форма поиска) открывает страницу бронирования
// по настройке в кабинете. Переводим на /en/booking с теми же параметрами.
Route::get('lang/{locale}/{path?}', function (Request $request, string $locale, ?string $path = null) {
    abort_unless(Locales::isSupported($locale), 404);
    $url = '/'.$locale.($path ? '/'.ltrim($path, '/') : '');

    return redirect($url.($request->getQueryString() ? '?'.$request->getQueryString() : ''));
})->where('path', '.*')->withoutMiddleware($publicWithoutSession);

Route::get('sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('robots.txt', fn () => response(implode("\n", [
    'User-agent: *',
    'Disallow: /admin',
    'Disallow: /*/booking',
    'Sitemap: '.route('sitemap'),
]))->header('Content-Type', 'text/plain'));

Route::prefix('{locale}')
    ->where(['locale' => implode('|', Locales::codes())])
    ->middleware(['locale', 'page-cache'])
    ->withoutMiddleware($publicWithoutSession)
    ->group(function () {
        Route::get('/', HomeController::class)->name('home');

        Route::controller(DestinationController::class)->prefix('destinations')->name('destinations.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('{hotel}', 'show')->name('show');
            Route::get('{hotel}/rooms', 'rooms')->name('rooms');
            Route::get('{hotel}/rooms/{room}', 'room')->name('rooms.show')->scopeBindings();
            Route::get('{hotel}/dining', 'dining')->name('dining');
            Route::get('{hotel}/experiences', 'experiences')->name('experiences');
            Route::get('{hotel}/offers', 'offers')->name('offers');
            Route::get('{hotel}/gallery', 'gallery')->name('gallery');
            Route::get('{hotel}/getting-here', 'gettingHere')->name('getting-here');
        });

        // Короткие адреса режима «один отель». В режиме сети — 301 на /destinations/{отель}/...
        Route::controller(SingleHotelController::class)->name('hotel.')->group(function () {
            Route::get('rooms', 'rooms')->name('rooms');
            Route::get('rooms/{room}', 'room')->name('rooms.show');
            Route::get('dining', 'dining')->name('dining');
            Route::get('gallery', 'gallery')->name('gallery');
            Route::get('getting-here', 'gettingHere')->name('getting-here');
        });

        Route::get('experiences', [ExperienceController::class, 'index'])->name('experiences.index');
        Route::get('experiences/{experience}', [ExperienceController::class, 'show'])->name('experiences.show');

        Route::get('events', [EventController::class, 'index'])->name('events.index');
        Route::get('events/{event}', [EventController::class, 'show'])->name('events.show');

        Route::get('offers', [OfferController::class, 'index'])->name('offers.index');
        Route::get('offers/{offer}', [OfferController::class, 'show'])->name('offers.show');

        Route::get('stories', [StoryController::class, 'index'])->name('stories.index');
        Route::get('stories/{story}', [StoryController::class, 'show'])->name('stories.show');

        Route::get('booking', BookingController::class)->name('booking');
        Route::get('map', MapController::class)->name('map');
        Route::get('gifts', GiftController::class)->name('gifts');
        Route::get('contact', [PageController::class, 'contact'])->name('contact');

        // Последним: страницы бренда по слагу.
        Route::get('{page}', [PageController::class, 'show'])->name('page');
    });
