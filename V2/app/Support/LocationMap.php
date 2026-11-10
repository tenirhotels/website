<?php

namespace App\Support;

use App\Models\Location;
use Illuminate\Support\Collection;

/**
 * Карта локации (locations.map): иллюстрация, станции, метки, время в пути, GPS-привязка.
 * Всё, что относится к месту, хранится в локации; у другого курорта — своя карта и свой транспорт.
 *
 * Структура JSON:
 *  enabled, width, height, one_way_minutes,
 *  images {winter, summer, winter_sm, summer_sm}  — путь на диске public или адрес от корня сайта (/images/…),
 *  stations [{key, lift, next, name{}, tag{}}]    — по порядку подъёма; key = папка панорамы public/panoramas/{key},
 *  hotel_panorama                                  — папка панорамы у отеля (public/panoramas/{…}),
 *  pins [{id, kind, n, x, y, seasons[], detail, cable, label{}}],
 *  geo_points [{lat, lng, x, y}],
 *  overlay [{kind: total|name|time|arrow_time|arrow|text, x, y, rot, station, text{}}]  — подписи поверх картинки.
 */
class LocationMap
{
    public const KINDS = ['restaurant', 'activity', 'station', 'hotel', 'place', 'trail'];

    private function __construct(private readonly array $d) {}

    public static function for(?Location $location): ?self
    {
        $d = $location?->map;
        if (! is_array($d) || ($d['enabled'] ?? true) === false || empty($d['pins'])) {
            return null;
        }

        return new self($d);
    }

    public function width(): int
    {
        return (int) ($this->d['width'] ?? 1667);
    }

    public function height(): int
    {
        return (int) ($this->d['height'] ?? 1179);
    }

    public function oneWay(): int
    {
        return (int) ($this->d['one_way_minutes'] ?? 0);
    }

    /** @return Collection<int, array{key: string, lift: ?string, next: ?int, name: string, tag: string}> */
    public function stations(): Collection
    {
        return collect($this->d['stations'] ?? [])->values()->map(fn ($s, $i) => [
            'key' => (string) $s['key'],
            'lift' => $s['lift'] ?? null,
            'next' => filled($s['next'] ?? null) ? (int) $s['next'] : null,
            'name' => $this->text($s['name'] ?? [], $s['key']),
            'tag' => $this->text($s['tag'] ?? [], $this->text($s['name'] ?? [], $s['key'])),
        ]);
    }

    /** Метки; подписи уже выбраны на текущем языке. */
    public function pins(): Collection
    {
        $stations = $this->stations()->keyBy('key');

        return collect($this->d['pins'] ?? [])->map(function ($p) use ($stations) {
            $label = $this->text($p['label'] ?? [], (string) ($p['id'] ?? ''));
            $tag = isset($p['cable']) && $stations->has($p['cable']) ? $stations[$p['cable']]['tag'] : $label;
            $p['label'] = $label;
            $p['tag'] = $tag;
            $p['x'] = (int) $p['x'];
            $p['y'] = (int) $p['y'];
            $p['seasons'] = ($p['seasons'] ?? []) ?: null;
            $p['n'] = filled($p['n'] ?? null) ? (int) $p['n'] : null;

            return $p;
        })->values();
    }

    /** Метка по id (метки станций — st-{key}, отеля — hotel). */
    public function pin(string $id): ?array
    {
        return $this->pins()->firstWhere('id', $id);
    }

    /** @return list<array{0: float, 1: float, 2: int, 3: int}> */
    public function geoPoints(): array
    {
        return collect($this->d['geo_points'] ?? [])
            ->filter(fn ($g) => filled($g['lat'] ?? null) && filled($g['lng'] ?? null) && filled($g['x'] ?? null) && filled($g['y'] ?? null))
            ->map(fn ($g) => [(float) $g['lat'], (float) $g['lng'], (int) $g['x'], (int) $g['y']])->values()->all();
    }

    /** Подписи поверх картинки с текстом уже подставленным. */
    public function overlay(): Collection
    {
        $stations = $this->stations()->keyBy('key');

        return collect($this->d['overlay'] ?? [])->map(function ($o) use ($stations) {
            $st = $stations[$o['station'] ?? ''] ?? null;
            $text = match ($o['kind'] ?? 'text') {
                'total' => __('map.one_way_at', ['min' => $this->oneWay(), 'unit' => __('map.min')]),
                'name' => $st['tag'] ?? '',
                'time', 'arrow_time' => $st && $st['next'] ? '≈'.$st['next'].' '.__('map.min') : '',
                'arrow' => $this->text($o['text'] ?? [], $st && $st['next'] ? '≈'.$st['next'].' '.__('map.min') : ''),
                default => $this->text($o['text'] ?? [], ''),
            };

            return ['kind' => $o['kind'] ?? 'text', 'x' => (int) $o['x'], 'y' => (int) $o['y'], 'rot' => (float) ($o['rot'] ?? 0), 'text' => $text];
        })->filter(fn ($o) => $o['text'] !== '')->values();
    }

    /** Адрес картинки: winter|summer, small — уменьшенная (если есть). */
    public function image(string $season, bool $small = false): ?string
    {
        $img = $this->d['images'] ?? [];
        $path = ($small ? ($img[$season.'_sm'] ?? null) : null) ?: ($img[$season] ?? null) ?: ($img[$season === 'winter' ? 'summer' : 'winter'] ?? null);

        return $this->url($path);
    }

    public function hotelPanorama(): ?string
    {
        return $this->d['hotel_panorama'] ?? null;
    }

    private function url(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return str_starts_with($path, '/') || str_starts_with($path, 'http') ? $path : media($path);
    }

    private function text(array|string|null $labels, string $fallback = ''): string
    {
        if (is_string($labels)) {
            return $labels;
        }

        return (string) (Locales::pick(array_filter((array) $labels, 'filled')) ?: $fallback);
    }

    /**
     * Зоны высадки (значок «такси» на иллюстрации): у Медеу и у станции Шымбулак.
     * Координаты — в пикселях карты 1667 × 1179 (найдены по значкам на картинке).
     */
    public static function dropoffPins(): array
    {
        return [
            ['id' => 'dropoff-medeu', 'kind' => 'place', 'x' => 305, 'y' => 758, 'label' => [
                'kk' => 'Түсіру аймағы · Медеу', 'ru' => 'Зона высадки · Медеу', 'en' => 'Drop-off zone · Medeu', 'zh' => '下客区 · Medeu']],
            ['id' => 'dropoff-shymbulak', 'kind' => 'place', 'x' => 817, 'y' => 728, 'label' => [
                'kk' => 'Түсіру аймағы · Шымбұлақ', 'ru' => 'Зона высадки · Шымбулак', 'en' => 'Drop-off zone · Shymbulak', 'zh' => '下客区 · Shymbulak']],
        ];
    }

    /**
     * Карта курорта Шымбулак (данные из V1: ResortMap и lang-файлы map) — для миграции и сидера.
     * Дальше правится в админке: Локация → «Карта».
     */
    public static function shymbulakDefaults(): array
    {
        $locales = ['kk', 'ru', 'en', 'zh'];
        $tr = function (string $key) use ($locales) {
            $out = [];
            foreach ($locales as $l) {
                $v = trans($key, [], $l);
                if ($v !== $key) {
                    $out[$l] = $v;
                }
            }

            return $out;
        };

        $stations = array_map(fn ($c) => [
            'key' => $c['key'], 'lift' => $c['lift'] ?? null, 'next' => $c['next'],
            'name' => $tr('map.names.station_'.$c['key']), 'tag' => $tr('map.tags.station_'.$c['key']),
        ], ResortMap::CABLE);

        $pins = array_map(function ($p) use ($tr) {
            $out = ['id' => $p['id'], 'kind' => $p['kind'], 'x' => $p['x'], 'y' => $p['y'], 'label' => $p['kind'] === 'hotel' ? [] : $tr('map.names.'.$p['name'])];
            foreach (['n', 'seasons', 'cable'] as $k) {
                if (isset($p[$k])) {
                    $out[$k] = $p[$k];
                }
            }
            if ($p['detail'] ?? false) {
                $out['detail'] = true;
            }

            return $out;
        }, ResortMap::pins());

        $extra = [];
        foreach (['kk' => 'мин', 'ru' => 'мин', 'en' => 'min', 'zh' => '分钟'] as $l => $u) {
            $extra[$l] = '≈15 '.$u;
        }
        $overlay = [
            ['kind' => 'total', 'x' => 20, 'y' => 490, 'rot' => 0],
            ['kind' => 'arrow', 'x' => 176, 'y' => 788, 'rot' => 35, 'text' => $extra],
        ];
        foreach ([[52, 'medeu'], [224, 'shymbulak'], [348, 'combi1'], [400, 'combi2']] as [$x, $k]) {
            $overlay[] = ['kind' => 'name', 'x' => $x, 'y' => 520, 'rot' => 0, 'station' => $k];
        }
        foreach ([['medeu', 138, [445, 815, -12]], ['shymbulak', 286, [832, 514, -34]], ['combi1', 374, [1110, 331, -24]]] as [$k, $dx, $a]) {
            $overlay[] = ['kind' => 'time', 'x' => $dx, 'y' => 552, 'rot' => 0, 'station' => $k];
            $overlay[] = ['kind' => 'arrow_time', 'x' => $a[0], 'y' => $a[1], 'rot' => $a[2], 'station' => $k];
        }

        $pins = [...$pins, ...self::dropoffPins()];

        return [
            'enabled' => true,
            'width' => ResortMap::WIDTH, 'height' => ResortMap::HEIGHT, 'one_way_minutes' => ResortMap::ONE_WAY_MINUTES,
            'images' => [
                'winter' => '/images/map/winter.webp', 'winter_sm' => '/images/map/winter-sm.webp',
                'summer' => '/images/map/summer.webp', 'summer_sm' => '/images/map/summer-sm.webp',
            ],
            'stations' => $stations, 'hotel_panorama' => 'tenir', 'pins' => $pins,
            'geo_points' => array_map(fn ($g) => ['lat' => $g[0], 'lng' => $g[1], 'x' => $g[2], 'y' => $g[3]], ResortMap::geoPoints()),
            'overlay' => $overlay,
        ];
    }
}
