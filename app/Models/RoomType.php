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

    public const FACTS = ['size' => 'Площадь', 'guests' => 'Гости', 'bed' => 'Кровать', 'view' => 'Вид'];

    /** Максимум крупных выделений. */
    public const MAX_TILES = 3;

    /** Значение параметра номера на языке посетителя. */
    public function factValue(string $key): ?string
    {
        return match ($key) {
            'size' => $this->size_m2 ? $this->size_m2.' м²' : null,
            'guests' => $this->max_guests ? __('site.hotel.up_to_guests', ['count' => $this->max_guests]) : null,
            'bed' => $this->t('bed') ?: null,
            'view' => $this->t('view') ?: null,
            default => null,
        };
    }

    /** Старые списки по языкам → элементы списка (по порядку, как были выровнены). */
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
            $items[] = ['source' => 'custom', 'icon' => amenity_icon_name((string) $ru), 'pin' => false, 'text' => $text];
        }

        return $items;
    }

    /** Элементы-параметры (площадь, гости, кровать, вид); первые три выделены. */
    public static function factItems(): array
    {
        $items = [];
        foreach (array_keys(self::FACTS) as $i => $k) {
            $items[] = ['source' => $k, 'icon' => $k, 'pin' => $i < 3, 'text' => []];
        }

        return $items;
    }

    /** Единый список по умолчанию: параметры + старые удобства. */
    public function defaultFeatures(): array
    {
        return array_merge(self::factItems(), $this->featuresFromLegacy());
    }

    /** @return list<array{source:string,icon:string,pin:bool,text:string}> Всё по порядку, на языке посетителя. */
    public function featureList(): array
    {
        $items = is_array($this->features) && $this->features !== [] ? $this->features : $this->defaultFeatures();
        $out = [];
        foreach ($items as $f) {
            $source = $f['source'] ?? 'custom';
            $text = isset(self::FACTS[$source]) ? $this->factValue($source) : \App\Support\Locales::pick((array) ($f['text'] ?? []));
            if (filled($text)) {
                $out[] = ['source' => $source, 'icon' => $f['icon'] ?? (isset(self::FACTS[$source]) ? $source : amenity_icon_name($text)), 'pin' => (bool) ($f['pin'] ?? false), 'text' => $text];
            }
        }

        return $out;
    }

    /** Только собственные удобства (для микроразметки). @return list<string> */
    public function amenityList(): array
    {
        return array_column(array_filter($this->featureList(), fn ($f) => $f['source'] === 'custom'), 'text');
    }

    /** Крупные выделения: отмеченные, не больше MAX_TILES. @return list<array{icon:string,text:string}> */
    public function tiles(): array
    {
        $tiles = [];
        foreach ($this->featureList() as $f) {
            if ($f['pin']) {
                $tiles[] = ['icon' => $f['icon'], 'text' => $f['text']];
            }
        }

        return array_slice($tiles, 0, self::MAX_TILES);
    }

    /** Остальное списком. @return list<array{icon:string,pin:bool,text:string}> */
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
