<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\SiteSettings;
use App\Filament\Resources\Events\EventResource;
use App\Filament\Resources\Hotels\HotelResource;
use App\Filament\Resources\Offers\OfferResource;
use App\Filament\Resources\Stories\StoryResource;
use Filament\Widgets\Widget;

class QuickActions extends Widget
{
    protected static ?int $sort = 0;

    protected string $view = 'filament.widgets.quick-actions';

    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        return ['actions' => [
            ['Открыть сайт', url('/'), 'heroicon-o-arrow-top-right-on-square', true],
            ['Новое предложение', OfferResource::getUrl('create'), 'heroicon-o-tag', false],
            ['Новое событие', EventResource::getUrl('create'), 'heroicon-o-calendar-days', false],
            ['Новая история', StoryResource::getUrl('create'), 'heroicon-o-newspaper', false],
            ['Отели и фото', HotelResource::getUrl(), 'heroicon-o-building-office-2', false],
            ['Настройки сайта', SiteSettings::getUrl(), 'heroicon-o-cog-6-tooth', false],
        ]];
    }
}
