<?php

namespace App\Providers;

use App\Models\Hotel;
use App\Models\Page;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Режим сайта (один отель / сеть) вычисляется заново для каждого запуска приложения.
        \App\Support\Property::flush();

        // Данные для шапки, футера и плашки бронирования на всех страницах.
        View::composer(['components.site.header', 'components.site.footer', 'components.site.booking-bar'], function ($view) {
            // Один запрос к БД на страницу (кэш в атрибутах текущего запроса).
            $attrs = request()->attributes;
            if (! $attrs->has('tenir.nav')) {
                $attrs->set('tenir.nav', [
                    'navHotels' => Hotel::published()->ordered()->get(),
                    'navPages' => Page::published()->whereIn('placement', ['main', 'footer'])->ordered()->get(),
                ]);
            }
            $view->with($attrs->get('tenir.nav'));
        });
    }
}
