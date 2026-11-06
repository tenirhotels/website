<?php

namespace App\Models;

use App\Models\Concerns\HasSeo;
use App\Models\Concerns\HasTranslations;
use App\Support\Property;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hotel extends Model
{
    use HasSeo, HasTranslations;

    public const STATUS_OPEN = 'open';
    public const STATUS_COMING_SOON = 'coming_soon';

    protected $guarded = ['id'];

    protected array $translatable = [
        'name', 'tagline', 'summary', 'description', 'opening_label',
        'region', 'city', 'address', 'getting_here',
    ];

    protected string $titleAttribute = 'name';

    protected $casts = [
        'gallery' => 'array',
        'highlights' => 'array',
        'reviews_widget' => 'array',
        'is_featured' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    protected static function booted(): void
    {
        // Публикация / снятие отеля может переключить режим сайта.
        static::saved(fn () => Property::flush());
        static::deleted(fn () => Property::flush());
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(RoomType::class);
    }

    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class);
    }

    public function experiences(): HasMany
    {
        return $this->hasMany(Experience::class);
    }

    public function venues(): HasMany
    {
        return $this->hasMany(Venue::class);
    }

    public function faqs(): HasMany
    {
        return $this->hasMany(Faq::class);
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    public function isOpen(): bool
    {
        return $this->status === self::STATUS_OPEN;
    }

    public function exelyContext(): string
    {
        return $this->exely_context ?: config('tenir.exely.context');
    }

    /** Обзор отеля: в режиме одного отеля — главная, в сети — /destinations/{slug}. */
    public function url(?string $locale = null): string
    {
        return Property::hotelUrl($this, 'overview', [], $locale);
    }

    /** Раздел отеля: rooms, room, dining, experiences, offers, gallery, getting-here. */
    public function sectionUrl(string $section, array $params = [], ?string $locale = null): string
    {
        return Property::hotelUrl($this, $section, $params, $locale);
    }
}
