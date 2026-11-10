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

    /**
     * Удобства без повторов того, что уже вынесено в ключевые факты (кровать, вид, площадь, гости).
     *
     * @return list<string>
     */
    public function extraAmenities(): array
    {
        $skip = [];
        if ($this->t('bed')) {
            array_push($skip, 'кроват', 'king', 'queen', 'twin', 'bed', 'төсек');
        }
        if ($this->t('view')) {
            array_push($skip, 'панорам', 'вид на', 'view', 'көрініс');
        }

        return array_values(array_filter($this->amenityList(), function ($a) use ($skip) {
            $t = mb_strtolower($a);
            foreach ($skip as $w) {
                if (str_contains($t, $w)) {
                    return false;
                }
            }

            return true;
        }));
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
