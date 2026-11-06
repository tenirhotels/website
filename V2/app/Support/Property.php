<?php

namespace App\Support;

use App\Models\Hotel;
use App\Models\Setting;

/**
 * Режим сайта: один отель или сеть (multi-property).
 *
 *  single — опубликован один отель. Сайт работает как сайт этого отеля:
 *           /ru (обзор отеля), /ru/rooms, /ru/rooms/{номер}, /ru/dining, /ru/gallery, /ru/getting-here.
 *  multi  — опубликовано два отеля и больше (включая «скоро открытие»). Сайт бренда:
 *           /ru (витрина), /ru/destinations, /ru/destinations/{отель}/rooms ...
 *
 * По умолчанию режим определяется автоматически; в настройках можно закрепить вручную.
 * При смене режима адреса другого режима отдают 301 на актуальные.
 */
class Property
{
    public const SINGLE = 'single';
    public const MULTI = 'multi';

    /** Разделы отеля и маршруты в каждом режиме. */
    private const ROUTES = [
        'overview' => ['single' => 'home', 'multi' => 'destinations.show'],
        'rooms' => ['single' => 'hotel.rooms', 'multi' => 'destinations.rooms'],
        'room' => ['single' => 'hotel.rooms.show', 'multi' => 'destinations.rooms.show'],
        'dining' => ['single' => 'hotel.dining', 'multi' => 'destinations.dining'],
        'experiences' => ['single' => 'experiences.index', 'multi' => 'destinations.experiences'],
        'offers' => ['single' => 'offers.index', 'multi' => 'destinations.offers'],
        'gallery' => ['single' => 'hotel.gallery', 'multi' => 'destinations.gallery'],
        'getting-here' => ['single' => 'hotel.getting-here', 'multi' => 'destinations.getting-here'],
    ];

    private static ?array $state = null;

    public static function flush(): void
    {
        self::$state = null;
    }

    private static function state(): array
    {
        if (self::$state !== null) {
            return self::$state;
        }

        try {
            $hotels = Hotel::published()->ordered()->get();
        } catch (\Throwable) {
            $hotels = collect(); // до миграций
        }

        $forced = Setting::get('site.mode', 'auto');
        $mode = in_array($forced, [self::SINGLE, self::MULTI], true)
            ? $forced
            : ($hotels->count() > 1 ? self::MULTI : self::SINGLE);

        $primary = $hotels->firstWhere('is_featured', true) ?? $hotels->first();

        return self::$state = ['mode' => $mode, 'primary' => $primary, 'count' => $hotels->count()];
    }

    public static function mode(): string
    {
        return self::state()['mode'];
    }

    public static function isSingle(): bool
    {
        return self::mode() === self::SINGLE;
    }

    public static function isMulti(): bool
    {
        return self::mode() === self::MULTI;
    }

    /** Отель, о котором сайт в режиме одного отеля (и «главный» в сети). */
    public static function primary(): ?Hotel
    {
        return self::state()['primary'];
    }

    public static function publishedCount(): int
    {
        return self::state()['count'];
    }

    /** Отель показывается по коротким адресам (режим одного отеля, это его отель). */
    public static function isSingleHotel(?Hotel $hotel): bool
    {
        return $hotel && self::isSingle() && self::primary()?->is($hotel);
    }

    public static function routeName(string $section, ?string $mode = null): string
    {
        return self::ROUTES[$section][$mode ?? self::mode()];
    }

    /** Адрес раздела отеля в текущем режиме. */
    public static function hotelUrl(Hotel $hotel, string $section = 'overview', array $params = [], ?string $locale = null): string
    {
        $mode = self::isSingleHotel($hotel) ? self::SINGLE : self::MULTI;
        $params['locale'] = $locale ?? app()->getLocale();
        if ($mode === self::MULTI) {
            $params['hotel'] = $hotel->slug;
        }

        return route(self::routeName($section, $mode), $params);
    }

    /** Слаги, занятые короткими адресами одного отеля. */
    public static function reservedSlugs(): array
    {
        return ['rooms', 'dining', 'gallery', 'getting-here', 'map', 'gifts', 'events'];
    }
}
