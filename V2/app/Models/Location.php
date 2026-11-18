<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Локация — место, где стоят отели: адрес, координаты, высота, «как добраться», транспорт (канатная дорога, трансфер…).
 * Сначала создаётся локация со всеми настройками, затем к ней привязываются отели; у отеля остаётся только своё.
 */
class Location extends Model
{
    use HasTranslations;

    protected $guarded = ['id'];

    protected array $translatable = ['name', 'region', 'city', 'address', 'getting_here', 'arrival_info', 'getting_tease', 'before_tease', 'lead', 'about'];

    protected string $titleAttribute = 'name';

    protected $casts = ['latitude' => 'float', 'longitude' => 'float', 'map' => 'array', 'levels' => 'array', 'place' => 'array'];

    // ------------------------------------------------------------------ страница локации /place/{slug}

    /** Страница включена и есть что показать (хотя бы название). */
    public function hasPlacePage(): bool
    {
        return (bool) data_get($this->place, 'enabled', false);
    }

    public function placeUrl(?string $locale = null): string
    {
        return route('place', ['locale' => $locale ?? app()->getLocale(), 'location' => $this->slug]);
    }

    /** Первая локация со включённой страницей (ссылки из подвала и со страниц отеля в режиме одного отеля). */
    public static function withPlacePage(): ?self
    {
        return static::query()->orderBy('sort')->orderBy('id')->get()->first(fn (self $l) => $l->hasPlacePage());
    }

    /** Строки «Факты»: [название, значение] на текущем языке. Первой — высота, если задана. */
    public function placeFacts(): array
    {
        $facts = collect(data_get($this->place, 'facts', []))
            ->map(fn ($f) => [\App\Support\Locales::pick($f['label'] ?? []), \App\Support\Locales::pick($f['value'] ?? [])])
            ->filter(fn ($f) => filled($f[0]) && filled($f[1]));

        if ($this->altitude_m && ! data_get($this->place, 'hide_altitude')) {
            $facts->prepend([__('site.place.altitude'), number_format($this->altitude_m, 0, ',', "\u{00A0}").' '.__('site.place.m')]);
        }

        return $facts->values()->all();
    }

    /** Камеры: [title, image, embed, page_url, refresh]. Без адреса ни картинки, ни страницы — камера не показывается. */
    public function placeCameras(): array
    {
        return collect(data_get($this->place, 'cameras', []))
            ->map(fn ($c) => [
                'title' => \App\Support\Locales::pick($c['title'] ?? []),
                'image' => filled($c['image_url'] ?? null) ? $c['image_url'] : null,
                'embed' => filled($c['embed_url'] ?? null) ? $c['embed_url'] : null,
                'page' => filled($c['page_url'] ?? null) ? $c['page_url'] : null,
                'refresh' => max(30, (int) ($c['refresh'] ?? 120)),
            ])
            ->filter(fn ($c) => $c['image'] || $c['embed'] || $c['page'])->values()->all();
    }

    /** Трассы по сложности: ['green' => [...], 'blue' => ..., ...] — каждая: name, length_m, status, note. */
    public function placeTrails(): array
    {
        return collect(data_get($this->place, 'trails', []))
            ->map(fn ($t) => [
                'name' => \App\Support\Locales::pick($t['name'] ?? []) ?: ($t['num'] ?? ''),
                'level' => $t['level'] ?? 'blue',
                'length_m' => (int) ($t['length_m'] ?? 0),
                'status' => $t['status'] ?? 'open',
                'note' => \App\Support\Locales::pick($t['note'] ?? []),
            ])
            ->filter(fn ($t) => filled($t['name']))
            ->groupBy('level')
            ->sortBy(fn ($g, $level) => ($i = array_search($level, ['green', 'blue', 'red', 'black', 'other'], true)) === false ? 99 : $i)
            ->map(fn ($g) => $g->values()->all())->all();
    }

    /** Точки погоды: [name, altitude_m]. Нет заданных — одна точка на высоте локации. */
    public function weatherPoints(): array
    {
        if (data_get($this->place, 'weather.enabled', true) === false || ! $this->latitude || ! $this->longitude) {
            return [];
        }
        $points = collect(data_get($this->place, 'weather.points', []))
            ->filter(fn ($p) => ($p['altitude_m'] ?? 0) > 0)
            ->map(fn ($p) => ['name' => \App\Support\Locales::pick($p['name'] ?? []), 'altitude_m' => (int) $p['altitude_m']])
            ->values()->all();

        return $points ?: ($this->altitude_m ? [['name' => null, 'altitude_m' => (int) $this->altitude_m]] : []);
    }

    public function hotels(): HasMany
    {
        return $this->hasMany(Hotel::class);
    }

    public function transports(): HasMany
    {
        return $this->hasMany(Transport::class)->published()->ordered();
    }

    /** До отелей этой локации ведёт канатная дорога курорта: есть опубликованная линия. */
    public function hasCable(): bool
    {
        return $this->transports->contains('kind', 'cable_line');
    }

    /** Карта локации (иллюстрация, станции, метки) — null, если не включена или не заполнена. */
    public function resortMap(): ?\App\Support\LocationMap
    {
        return \App\Support\LocationMap::for($this);
    }

    public function transportByCode(string $code): ?Transport
    {
        return $this->transports->first(fn ($t) => $t->kind === 'cable_line' && $t->code === $code);
    }

    /**
     * Данные канатной дороги из записей «Транспорт» (для страницы «Как добраться»).
     * Ключи: hours_medeu (M-1), hours_combi1 (K-1), hours_combi2 (K-2), hours_note, guest_from/guest_to,
     * photo_medeu (M-1), photo_shymbulak (K-1), photo_combi1 (K-2) — фото станции, откуда идёт линия.
     */
    public function cableSetting(string $key, mixed $default = null): mixed
    {
        $line = ['hours_medeu' => 'M-1', 'hours_combi1' => 'K-1', 'hours_combi2' => 'K-2',
            'photo_medeu' => 'M-1', 'photo_shymbulak' => 'K-1', 'photo_combi1' => 'K-2'][$key] ?? null;
        $v = null;
        if ($line) {
            $t = $this->transportByCode($line);
            $v = str_starts_with($key, 'photo_') ? $t?->image : $t?->hours;
        } elseif ($key === 'hours_note') {
            foreach ($this->transports->where('kind', 'cable_line') as $t) {
                if ($note = trim((string) $t->t('note'))) {
                    $v = $note;
                    break;
                }
            }
        } elseif ($key === 'guest_from' || $key === 'guest_to') {
            $hours = $this->transports->firstWhere('kind', 'guest_ride')?->hours;
            $parts = $hours ? preg_split('/\s*[–—-]\s*/u', $hours) : [];
            $v = $parts[$key === 'guest_from' ? 0 : 1] ?? null;
        }

        return $v === null || $v === '' ? $default : $v;
    }
}
