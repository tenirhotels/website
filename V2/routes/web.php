<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\ExperienceController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\StoryController;
use App\Support\Locales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Карта сайта Tenir Hotels
|--------------------------------------------------------------------------
| /{locale}                                     главная (пока один отель — о нём)
| /{locale}/destinations                        все отели и «скоро открытие»
| /{locale}/destinations/{hotel}                обзор отеля
| /{locale}/destinations/{hotel}/rooms[/{room}] номера
| /{locale}/destinations/{hotel}/dining         рестораны и бары
| /{locale}/destinations/{hotel}/experiences    впечатления отеля
| /{locale}/destinations/{hotel}/offers         предложения отеля
| /{locale}/destinations/{hotel}/gallery        галерея
| /{locale}/destinations/{hotel}/getting-here   как добраться, FAQ
| /{locale}/experiences[/{slug}]                впечатления всей сети
| /{locale}/offers[/{slug}]                     спецпредложения
| /{locale}/stories[/{slug}]                    истории и новости
| /{locale}/booking                             модуль бронирования Exely
| /{locale}/contact                             контакты
| /{locale}/{page}                              страницы бренда: about, sustainability, ...
*/

Route::get('/', fn (Request $request) => redirect('/'.Locales::fromBrowser($request->header('Accept-Language'))));

Route::get('sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('robots.txt', fn () => response(implode("\n", [
    'User-agent: *',
    'Disallow: /admin',
    'Disallow: /*/booking',
    'Sitemap: '.route('sitemap'),
]))->header('Content-Type', 'text/plain'));

Route::prefix('{locale}')
    ->where(['locale' => implode('|', Locales::codes())])
    ->middleware('locale')
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

        Route::get('experiences', [ExperienceController::class, 'index'])->name('experiences.index');
        Route::get('experiences/{experience}', [ExperienceController::class, 'show'])->name('experiences.show');

        Route::get('offers', [OfferController::class, 'index'])->name('offers.index');
        Route::get('offers/{offer}', [OfferController::class, 'show'])->name('offers.show');

        Route::get('stories', [StoryController::class, 'index'])->name('stories.index');
        Route::get('stories/{story}', [StoryController::class, 'show'])->name('stories.show');

        Route::get('booking', BookingController::class)->name('booking');
        Route::get('contact', [PageController::class, 'contact'])->name('contact');

        // Последним: страницы бренда по слагу.
        Route::get('{page}', [PageController::class, 'show'])->name('page');
    });
