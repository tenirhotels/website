<?php

namespace App\Models;

use App\Models\Concerns\HasSeo;
use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Событие афиши: дата (или период), время, место. Прошедшие скрываются автоматически. */
class Event extends Model
{
    use HasSeo, HasTranslations;

    protected $guarded = ['id'];

    protected array $translatable = ['title', 'summary', 'description', 'place'];

    protected $casts = [
        'starts_on' => 'date',
        'ends_on' => 'date',
        'is_featured' => 'boolean',
    ];

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function lastDay(): \Carbon\CarbonInterface
    {
        return $this->ends_on ?? $this->starts_on;
    }

    /** Опубликовано и ещё не закончилось. */
    public function scopeUpcoming(Builder $query): Builder
    {
        $today = now()->toDateString();

        return $query->published()
            ->where(fn ($q) => $q->whereDate('ends_on', '>=', $today)
                ->orWhere(fn ($q) => $q->whereNull('ends_on')->whereDate('starts_on', '>=', $today)))
            ->orderBy('starts_on')->orderBy('sort')->orderBy('id');
    }

    public function isPast(): bool
    {
        return $this->lastDay()->endOfDay()->isPast();
    }

    /** «14 декабря» или «14–16 декабря» / «30 декабря – 2 января». */
    public function dateLabel(): string
    {
        $a = $this->starts_on->copy()->locale(app()->getLocale());
        $b = $this->ends_on?->copy()->locale(app()->getLocale());
        if (! $b || $b->isSameDay($a)) {
            return $a->translatedFormat('j F');
        }
        if ($a->isSameMonth($b) && $a->isSameYear($b)) {
            return $a->format('j').'–'.$b->translatedFormat('j F');
        }

        return $a->translatedFormat('j F').' – '.$b->translatedFormat('j F');
    }

    /** Крупная часть плашки даты: «15» или «15–28» (если месяцы разные — «30 – 2»). */
    public function dayBadge(): string
    {
        $a = $this->starts_on->copy()->locale(app()->getLocale());
        $b = $this->ends_on?->copy()->locale(app()->getLocale());

        return ($b && ! $b->isSameDay($a)) ? $a->format('j').'–'.$b->format('j') : $a->format('j');
    }

    /** Подпись месяца на плашке: «ноябрь» или «ноябрь – декабрь». */
    public function monthBadge(): string
    {
        $a = $this->starts_on->copy()->locale(app()->getLocale());
        $b = $this->ends_on?->copy()->locale(app()->getLocale());
        if (! $b || $a->isSameMonth($b)) {
            return $a->translatedFormat('F');
        }

        return $a->translatedFormat('F').' – '.$b->translatedFormat('F');
    }

    public function whenLabel(): string
    {
        return trim($this->dateLabel().($this->time_text ? ', '.$this->time_text : ''));
    }

    public function url(?string $locale = null): string
    {
        return route('events.show', ['locale' => $locale ?? app()->getLocale(), 'event' => $this->slug]);
    }
}
