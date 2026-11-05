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

    public function url(?string $locale = null): string
    {
        return route('destinations.rooms.show', [
            'locale' => $locale ?? app()->getLocale(),
            'hotel' => $this->hotel->slug,
            'room' => $this->slug,
        ]);
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
