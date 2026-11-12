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
        // Админка: везде один и тот же календарь вместо браузерного dd/mm/yyyy — дд.мм.гггг, неделя с понедельника.
        \Filament\Forms\Components\DateTimePicker::configureUsing(fn ($c) => $c->native(false)->firstDayOfWeek(1)->seconds(false)->displayFormat('d.m.Y H:i'));
        \Filament\Forms\Components\DatePicker::configureUsing(fn ($c) => $c->native(false)->firstDayOfWeek(1)->displayFormat('d.m.Y')->closeOnDateSelection());

        // Режим сайта (один отель / сеть) вычисляется заново для каждого запуска приложения.
        \App\Support\Property::flush();

        // Любое сохранение контента сбрасывает кэш готовых страниц.
        foreach ([Hotel::class, Page::class, \App\Models\RoomType::class, \App\Models\Venue::class, \App\Models\Transport::class, \App\Models\Location::class, \App\Models\Experience::class, \App\Models\Event::class,
                  \App\Models\Story::class, \App\Models\Offer::class, \App\Models\Faq::class, \App\Models\Recognition::class, \App\Models\Redirect::class] as $model) {
            $model::saved(fn () => \App\Support\PageCache::flush());
            $model::deleted(fn () => \App\Support\PageCache::flush());
        }

        // Данные для шапки, футера и плашки бронирования на всех страницах.
        View::composer(['components.site.header', 'components.site.footer', 'components.site.booking-bar'], function ($view) {
            // Один запрос к БД на страницу (кэш в атрибутах текущего запроса).
            $attrs = request()->attributes;
            if (! $attrs->has('tenir.nav')) {
                $attrs->set('tenir.nav', [
                    'navHotels' => Hotel::published()->ordered()->get(),
                    'navPages' => Page::published()->whereIn('placement', ['main', 'brand', 'footer'])->ordered()->get(),
                ]);
            }
            $view->with($attrs->get('tenir.nav'));
        });
    }
}
