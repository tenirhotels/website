<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Транспорт локации (как номера и рестораны — запись): линия канатной дороги, трансфер, такси. */
class Transport extends Model
{
    use HasTranslations;

    public const KINDS = [
        'cable_line' => 'Линия канатной дороги',
        'guest_ride' => 'Особый подъём для гостей отеля',
        'shuttle' => 'Трансфер',
        'taxi' => 'Такси',
        'other' => 'Другое',
    ];

    public const CABLE_CODES = ['M-1' => 'Медеу ↔ Шымбулак', 'K-1' => 'Шымбулак ↔ Средняя', 'K-2' => 'Средняя ↔ Верхняя'];

    protected $guarded = ['id'];

    protected array $translatable = ['name', 'summary', 'note', 'price', 'route_from', 'route_to'];

    protected string $titleAttribute = 'name';

    protected $casts = ['is_published' => 'boolean', 'orderable' => 'boolean', 'map_pins' => 'array'];

    /** Поездка «откуда → куда» (такси, трансфер). */
    public function isRide(): bool
    {
        return in_array($this->kind, ['taxi', 'shuttle'], true);
    }

    /** Заголовок карточки: название или «Откуда — Куда». */
    public function title(): string
    {
        $name = trim((string) $this->t('name'));
        if ($name !== '') {
            return $name;
        }
        $a = trim((string) $this->t('route_from'));
        $b = trim((string) $this->t('route_to'));

        return $a !== '' && $b !== '' ? $a.' — '.$b : (self::KINDS[$this->kind] ?? '');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort')->orderBy('id');
    }
}
