<?php

namespace App\Models;

use App\Models\Concerns\HasSeo;
use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Offer extends Model
{
    use \App\Models\Concerns\ForcesEditorHotel;
    use HasSeo, HasTranslations;

    protected $guarded = ['id'];

    protected array $translatable = ['title', 'summary', 'description', 'included', 'terms'];

    protected $casts = [
        'valid_from' => 'date',
        'valid_to' => 'date',
        'is_featured' => 'boolean',
    ];

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    /** Опубликовано и действует сегодня. */
    public function scopeActive(Builder $query): Builder
    {
        $today = now()->toDateString();

        return $query->published()
            ->where(fn ($q) => $q->whereNull('valid_from')->orWhereDate('valid_from', '<=', $today))
            ->where(fn ($q) => $q->whereNull('valid_to')->orWhereDate('valid_to', '>=', $today));
    }

    public function url(?string $locale = null): string
    {
        return route('offers.show', ['locale' => $locale ?? app()->getLocale(), 'offer' => $this->slug]);
    }

    public function bookingUrl(): string
    {
        return route('booking', array_filter([
            'locale' => app()->getLocale(),
            'hotel' => $this->hotel?->slug,
            'special_offer' => $this->exely_special_offer_id,
            'promo_code' => $this->promo_code,
        ]));
    }
}
