<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\SiteSettings;
use App\Filament\Resources\Events\EventResource;
use App\Filament\Resources\Experiences\ExperienceResource;
use App\Filament\Resources\Faqs\FaqResource;
use App\Filament\Resources\Hotels\HotelResource;
use App\Filament\Resources\Locations\LocationResource;
use App\Filament\Resources\RoomTypes\RoomTypeResource;
use App\Filament\Resources\Transports\TransportResource;
use App\Filament\Resources\Venues\VenueResource;
use App\Filament\Resources\Offers\OfferResource;
use App\Filament\Resources\Stories\StoryResource;
use Filament\Widgets\Widget;

class QuickActions extends Widget
{
    public static function canView(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    protected static ?int $sort = 0;

    protected string $view = 'filament.widgets.quick-actions';

    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        // «Создать» — одним выпадающим списком; сайт и настройки — отдельными кнопками.
        return [
            'create' => [
                ['Предложение', OfferResource::getUrl('create'), 'heroicon-o-tag'],
                ['Событие', EventResource::getUrl('create'), 'heroicon-o-calendar-days'],
                ['История или новость', StoryResource::getUrl('create'), 'heroicon-o-newspaper'],
                ['Впечатление', ExperienceResource::getUrl('create'), 'heroicon-o-sparkles'],
                ['Вопрос и ответ', FaqResource::getUrl('create'), 'heroicon-o-question-mark-circle'],
                ['Номер', RoomTypeResource::getUrl('create'), 'heroicon-o-home-modern'],
                ['Ресторан или бар', VenueResource::getUrl('create'), 'heroicon-o-cake'],
                ['Транспорт', TransportResource::getUrl('create'), 'heroicon-o-truck'],
                ['Отель', HotelResource::getUrl('create'), 'heroicon-o-building-office-2'],
                ['Локация', LocationResource::getUrl('create'), 'heroicon-o-map-pin'],
            ],
            'settingsUrl' => SiteSettings::getUrl(),
            'siteUrl' => url('/'),
        ];
    }
}
