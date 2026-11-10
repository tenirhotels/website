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
    ];

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    /** @return list<string> */
    public function amenityList(): array
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
        $chosen = $this->highlights ?? array_keys(self::FACTS);
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
