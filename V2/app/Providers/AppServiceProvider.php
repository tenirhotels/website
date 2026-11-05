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
        // Данные для шапки, футера и плашки бронирования на всех страницах.
        View::composer(['components.site.header', 'components.site.footer', 'components.site.booking-bar'], function ($view) {
            static $shared = null;
            $shared ??= [
                'navHotels' => Hotel::published()->ordered()->get(),
                'navPages' => Page::published()->whereIn('placement', ['main', 'footer'])->ordered()->get(),
            ];
            $view->with($shared);
        });
    }
}
