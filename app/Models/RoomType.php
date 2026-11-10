<?php

namespace App\Models;

use App\Models\Concerns\HasSeo;
use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomType extends Model
{
    use HasSeo, HasTranslations;

    protected $guarded = ['id'];

    protected array $translatable = ['name', 'summary', 'description', 'bed', 'view'];

    protected string $titleAttribute = 'name';

    protected $casts = [
        'amenities' => 'array',
        'gallery' => 'array',
        'highlights' => 'array',
        'features' => 'array',
    ];

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    /** Старый формат (списки строк по языкам) — пока features не заполнены. @return list<string> */
    private function legacyAmenities(): array
    {
        $all = $this->amenities ?? [];
        foreach (\App\Support\Locales::chain(app()->getLocale()) as $code) {
            if (! empty($all[$code])) {
                return array_values($all[$code]);
            }
        }

        return [];
    }

    /** Ключи фактов, выделяемых плитками; null в базе — все четыре. */
    public const FACTS = ['size' => 'Площадь', 'guests' => 'Гости', 'bed' => 'Кровать', 'view' => 'Вид'];

    /** @return list<array{0:string,1:string,2:string}> [ключ, подпись, значение] — только выбранные и заполненные. */
    public function highlightFacts(): array
    {
        $chosen = $this->highlights ?? ['size', 'guests', 'bed'];
        $values = [
            'size' => $this->size_m2 ? $this->size_m2.' м²' : null,
            'guests' => $this->max_guests ? __('site.hotel.up_to_guests', ['count' => $this->max_guests]) : null,
            'bed' => $this->t('bed'),
            'view' => $this->t('view'),
        ];
        $out = [];
        foreach (array_keys(self::FACTS) as $k) {
            if (in_array($k, $chosen, true) && $values[$k]) {
                $out[] = [$k, __('site.hotel.'.($k === 'guests' ? 'guests' : $k)), $values[$k]];
            }
        }

        return $out;
    }

    /** Старые списки по языкам → features (по порядку, как были выровнены). */
    public function featuresFromLegacy(): array
    {
        $old = (array) ($this->amenities ?? []);
        $base = $old['ru'] ?? $old['en'] ?? [];
        if (! is_array($base)) {
            return [];
        }
        $items = [];
        foreach (array_values($base) as $i => $ru) {
            $text = [];
            foreach ($old as $code => $list) {
                if (is_array($list) && count($list) === count($base) && filled($list[$i] ?? null)) {
                    $text[$code] = $list[$i];
                }
            }
            $text['ru'] ??= $ru;
            $items[] = ['icon' => amenity_icon_name((string) $ru), 'pin' => false, 'text' => $text];
        }

        return $items;
    }

    /** Максимум плиток-выделений (факты + закреплённые удобства). */
    public const MAX_TILES = 3;

    /** @return list<array{icon:string,pin:bool,text:string}> Все удобства по порядку, на языке посетителя. */
    public function featureList(): array
    {
        $out = [];
        if (is_array($this->features) && $this->features !== []) {
            foreach ($this->features as $f) {
                $text = \App\Support\Locales::pick((array) ($f['text'] ?? []));
                if (filled($text)) {
                    $out[] = ['icon' => $f['icon'] ?? amenity_icon_name($text), 'pin' => (bool) ($f['pin'] ?? false), 'text' => $text];
                }
            }

            return $out;
        }
        foreach ($this->legacyAmenities() as $text) {
            $out[] = ['icon' => amenity_icon_name($text), 'pin' => false, 'text' => $text];
        }

        return $out;
    }

    /** @return list<string> */
    public function amenityList(): array
    {
        return array_column($this->featureList(), 'text');
    }

    /** Плитки: выбранные факты, затем закреплённые удобства; не больше MAX_TILES. @return list<array{icon:string,text:string}> */
    public function tiles(): array
    {
        $tiles = array_map(fn ($f) => ['icon' => $f[0], 'text' => $f[2]], $this->highlightFacts());
        foreach ($this->featureList() as $f) {
            if ($f['pin']) {
                $tiles[] = ['icon' => $f['icon'], 'text' => $f['text']];
            }
        }

        return array_slice($tiles, 0, self::MAX_TILES);
    }

    /** Удобства списком: незакреплённые. @return list<array{icon:string,pin:bool,text:string}> */
    public function listFeatures(): array
    {
        return array_values(array_filter($this->featureList(), fn ($f) => ! $f['pin']));
    }

    public function url(?string $locale = null): string
    {
        return $this->hotel->sectionUrl('room', ['room' => $this->slug], $locale);
    }

    public function bookingUrl(): string
    {
        return route('booking', array_filter([
            'locale' => app()->getLocale(),
            'hotel' => $this->hotel->slug,
            'room_type' => $this->exely_room_type_id,
        ]));
    }
}
