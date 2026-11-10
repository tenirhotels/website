<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Транспорт к отелю (как номера и рестораны — запись локации): линия канатной дороги, трансфер, такси. */
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

    protected array $translatable = ['name', 'summary', 'note'];

    protected string $titleAttribute = 'name';

    protected $casts = ['is_published' => 'boolean'];

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
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
