<?php

namespace App\Support;

/**
 * Интерактивная карта курорта: данные меток и привязка к GPS.
 *
 * Координаты меток — в пикселях исходной иллюстрации (1667 × 1179), см. public/images/map/*.webp.
 * Названия и подписи — в lang/{locale}/map.php (ключ name → map.names.{name}).
 * Метки с 'seasons' показываются только в этом сезоне; без ключа — круглый год.
 */
class ResortMap
{
    public const WIDTH = 1667;

    public const HEIGHT = 1179;

    /**
     * Станции канатной дороги по порядку подъёма и участок до следующей станции.
     * lift — название канатной дороги, которая едет ОТ этой станции (M-1, K-1, K-2, как на карте курорта).
     * Станции: Медеу, Шымбулак, Средняя, Верхняя. Ключи combi1/combi2 — исторические (папки панорам, настройки), на сайте не показываются.
     */
    public const CABLE = [
        ['key' => 'medeu', 'lift' => 'M-1', 'next' => 22],
        ['key' => 'shymbulak', 'lift' => 'K-1', 'next' => 17],
        ['key' => 'combi1', 'lift' => 'K-2', 'next' => 9],
        ['key' => 'combi2', 'next' => null],
    ];

    /** Всего в одну сторону с пересадками, мин (по карте курорта). */
    public const ONE_WAY_MINUTES = 60;

    /** Месяцы зимнего оформления карты. */
    private const WINTER_MONTHS = [11, 12, 1, 2, 3, 4];

    /** @return list<array<string, mixed>> */
    public static function pins(): array
    {
        $r = fn (int $n, string $name, int $x, int $y) => ['id' => 'r'.$n, 'kind' => 'restaurant', 'n' => $n, 'name' => $name, 'x' => $x, 'y' => $y];
        $a = fn (string $season, int $n, string $name, int $x, int $y) => ['id' => 'a'.$season[0].$n, 'kind' => 'activity', 'n' => $n, 'name' => $name, 'x' => $x, 'y' => $y, 'seasons' => [$season]];
        $s = fn (string $key, int $x, int $y) => ['id' => 'st-'.$key, 'kind' => 'station', 'name' => 'station_'.$key, 'x' => $x, 'y' => $y, 'cable' => $key];

        return [
            $r(1, 'tenir_restaurant', 1080, 135),
            $r(2, 'la_skala', 1273, 438),
            $r(3, 'saray_bar', 562, 478),
            $r(4, 'restaurants_gallery', 837, 641),
            $r(5, 'refugio', 966, 767),
            $r(6, 'auyl', 162, 710),
            $r(7, 'gogra', 68, 959),

            $a('winter', 1, 'ice_skating', 443, 715),
            $a('winter', 2, 'hiking_winter', 952, 715),
            $a('winter', 3, 'skiing', 833, 393),

            $a('summer', 1, 'paragliding', 973, 190),
            $a('summer', 2, 'horse_rides', 729, 475),
            $a('summer', 3, 'rope_park_shymbulak', 977, 546),
            $a('summer', 4, 'hiking_climbing', 941, 715),
            $a('summer', 5, 'alpine_park', 504, 564),
            $a('summer', 6, 'rope_park_medeu', 302, 578),
            $a('summer', 7, 'gorilla_zipline', 207, 923),

            $s('medeu', 150, 925),
            $s('shymbulak', 640, 655),
            $s('combi1', 990, 400),
            $s('combi2', 1225, 255),
            ['id' => 'st-kkd4', 'kind' => 'station', 'name' => 'station_kkd4', 'x' => 1190, 'y' => 495],

            ['id' => 'hotel', 'kind' => 'hotel', 'name' => 'hotel', 'x' => 1170, 'y' => 185],
            ['id' => 'rink', 'kind' => 'place', 'name' => 'medeu_rink', 'x' => 352, 'y' => 765],

            ['id' => 'tr-glacier', 'kind' => 'trail', 'name' => 'trail_glacier', 'x' => 1470, 'y' => 290, 'detail' => true],
            ['id' => 'tr-tuyuksu', 'kind' => 'trail', 'name' => 'trail_tuyuksu', 'x' => 1195, 'y' => 845, 'detail' => true],
            ['id' => 'tr-walk', 'kind' => 'trail', 'name' => 'trail_walk', 'x' => 1100, 'y' => 470, 'detail' => true],
            ['id' => 'almaty', 'kind' => 'trail', 'name' => 'almaty', 'x' => 95, 'y' => 1105],
        ];
    }

    /**
     * Условные значки на карте (спрайты в public/images/map/legend).
     *
     * @return list<array{key: string, icon: string}>
     */
    public static function services(string $season): array
    {
        $common = [
            ['key' => 'info', 'icon' => 'info'],
            ['key' => 'rent', 'icon' => 'rent', 'wide' => true],
            ['key' => 'tickets', 'icon' => 'tickets'],
            ['key' => 'atm', 'icon' => 'atm'],
            ['key' => 'cable', 'icon' => 'cable'],
            ['key' => 'dropoff', 'icon' => 'dropoff'],
            ['key' => 'help', 'icon' => 'help'],
            ['key' => 'bus', 'icon' => 'bus'],
            ['key' => 'parking', 'icon' => 'parking'],
            ['key' => 'street', 'icon' => 'street'],
        ];

        return $season === 'winter'
            ? [...$common, ['key' => 'ski', 'icon' => 'winter-ski'], ['key' => 'wc', 'icon' => 'winter-wc']]
            : [...$common, ['key' => 'wc', 'icon' => 'summer-wc'], ['key' => 'nature', 'icon' => 'summer-nature']];
    }

    /** @return list<array{key: string, icon: string}> */
    public static function freeActivities(string $season): array
    {
        return [
            ['key' => 'stairs', 'icon' => $season.'-stairs'],
            ['key' => 'springs', 'icon' => $season.'-springs'],
        ];
    }

    /** Сезон оформления карты по умолчанию (на странице его уточняет JS по дате посетителя). */
    public static function defaultSeason(?\DateTimeInterface $date = null): string
    {
        return in_array((int) ($date ?? now())->format('n'), self::WINTER_MONTHS, true) ? 'winter' : 'summer';
    }

    /**
     * Опорные точки привязки к GPS: [широта, долгота, x, y на иллюстрации].
     * Карта схематичная (не в масштабе), поэтому «вы здесь» — приблизительно; чем больше точек — тем точнее.
     * Проверенные: гостиница (координаты отеля в базе), нижняя станция Шымбулака (Wikipedia), каток «Медеу».
     * TODO: добавить замеры с телефона на станциях K-1, K-2 и нижней станции Медеу.
     *
     * @return list<array{0: float, 1: float, 2: int, 3: int}>
     */
    public static function geoPoints(): array
    {
        return [
            [43.1573, 77.0589, 352, 722],   // каток «Медеу»
            [43.12806, 77.08083, 640, 655], // станция Шымбулак (курорт, 2260 м)
            [43.113991, 77.111328, 1170, 185], // Tenir Hotel Shymbulak
        ];
    }
}
