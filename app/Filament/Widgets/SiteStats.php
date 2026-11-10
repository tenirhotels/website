<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Events\EventResource;
use App\Filament\Resources\Hotels\HotelResource;
use App\Filament\Resources\Offers\OfferResource;
use App\Filament\Resources\RoomTypes\RoomTypeResource;
use App\Filament\Resources\Stories\StoryResource;
use App\Models\Event;
use App\Models\Hotel;
use App\Models\Offer;
use App\Models\RoomType;
use App\Models\Story;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/** Что сейчас видят гости: только опубликованное и действующее. */
class SiteStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $heading = 'Сейчас на сайте';

    protected function getStats(): array
    {
        return [
            Stat::make('Отели', Hotel::open()->published()->count())
                ->description('всего в базе: '.Hotel::count())->url(HotelResource::getUrl()),
            Stat::make('Номера', RoomType::published()->count())
                ->description('типов номеров на сайте')->url(RoomTypeResource::getUrl()),
            Stat::make('Предложения', Offer::active()->count())
                ->description('действуют сегодня (всего '.Offer::count().')')->url(OfferResource::getUrl()),
            Stat::make('События', Event::upcoming()->count())
                ->description('предстоящие в афише')->url(EventResource::getUrl()),
            Stat::make('Истории', Story::live()->count())
                ->description('опубликовано (всего '.Story::count().')')->url(StoryResource::getUrl()),
        ];
    }
}
