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

    protected array $translatable = ['name', 'region', 'city', 'address', 'getting_here'];

    protected string $titleAttribute = 'name';

    protected $casts = ['latitude' => 'float', 'longitude' => 'float', 'map' => 'array'];

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
