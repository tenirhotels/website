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

    public function transports(): HasMany
    {
        return $this->hasMany(Transport::class)->published()->ordered();
    }

    /** К отелю ведёт канатная дорога курорта: есть опубликованная линия (запись «Транспорт» с видом «Линия канатной дороги»). */
    public function hasCable(): bool
    {
        return $this->transports->contains('kind', 'cable_line');
    }

    private function transportByCode(string $code): ?Transport
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
