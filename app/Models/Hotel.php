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
        'name', 'tagline', 'summary', 'hero_line', 'description', 'opening_label',
    ];

    /** Настройки места живут в локации: отель читает (и пишет) их через неё — шаблоны и SEO работают как раньше. */
    private const LOCATION_ATTRS = ['region', 'city', 'address', 'getting_here', 'arrival_info', 'levels', 'altitude_m', 'country_code', 'latitude', 'longitude'];

    protected string $titleAttribute = 'name';

    protected $casts = [
        'gallery' => 'array',
        'highlights' => 'array',
        'reviews_widget' => 'array',
        'is_featured' => 'boolean',
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

    public function location(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function getAttribute($key)
    {
        return in_array($key, self::LOCATION_ATTRS, true) ? $this->location?->getAttribute($key) : parent::getAttribute($key);
    }

    public function fill(array $attributes)
    {
        // Колонок региона/адреса/координат у отеля больше нет — защита от массового заполнения их бы отбросила.
        foreach (self::LOCATION_ATTRS as $key) {
            if (array_key_exists($key, $attributes)) {
                $this->setAttribute($key, $attributes[$key]);
                unset($attributes[$key]);
            }
        }

        return parent::fill($attributes);
    }

    public function setAttribute($key, $value)
    {
        if (in_array($key, self::LOCATION_ATTRS, true)) {
            if ($loc = $this->location) {
                $loc->setAttribute($key, $value);
                $loc->save();
            }

            return $this;
        }

        return parent::setAttribute($key, $value);
    }

    /** Транспорт локации отеля (для страницы «Как добраться»). */
    public function getTransportsAttribute(): \Illuminate\Support\Collection
    {
        return $this->location?->transports ?? collect();
    }

    public function hasCable(): bool
    {
        return (bool) $this->location?->hasCable();
    }

    /** Карта курорта своей локации (null — у локации карты нет). */
    public function resortMap(): ?\App\Support\LocationMap
    {
        return $this->location?->resortMap();
    }

    public function hasMap(): bool
    {
        return $this->resortMap() !== null;
    }

    public function cableSetting(string $key, mixed $default = null): mixed
    {
        return $this->location ? $this->location->cableSetting($key, $default) : $default;
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    /** Фото героя по сезону (зима — hero_image, лето — hero_image_summer; нет летнего — всегда основное). */
    public function seasonHero(): ?string
    {
        if ($this->hero_image_summer && \App\Support\ResortMap::defaultSeason() === 'summer') {
            return $this->hero_image_summer;
        }

        return $this->hero_image;
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
