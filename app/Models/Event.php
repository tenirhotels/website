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
    use \App\Models\Concerns\ForcesEditorHotel;
    use HasSeo, HasTranslations;

    protected $guarded = ['id'];

    protected array $translatable = ['title', 'summary', 'description', 'place'];

    protected $casts = [
        'starts_on' => 'date',
        'ends_on' => 'date',
        'extra_dates' => 'array',
        'is_featured' => 'boolean',
    ];

    protected static function booted(): void
    {
        // Есть другие даты — список приводится в порядок, а «окончание» = последняя дата (по нему событие уходит с сайта).
        static::saving(function (self $event) {
            $extra = collect($event->extra_dates ?? [])->filter()
                ->map(fn ($d) => \Illuminate\Support\Carbon::parse($d)->toDateString())
                ->reject(fn ($d) => $event->starts_on && $d === $event->starts_on->toDateString())
                ->unique()->sort()->values();
            $event->extra_dates = $extra->isEmpty() ? null : $extra->all();
            if ($extra->isNotEmpty()) {
                $event->ends_on = $extra->last();
            }
        });
    }

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

    /** Событие в несколько отдельных дат (не подряд). */
    public function isRecurring(): bool
    {
        return ! empty($this->extra_dates);
    }

    /** Все дни проведения по порядку: основная дата и другие. @return \Illuminate\Support\Collection<int, \Carbon\CarbonInterface> */
    public function days(): \Illuminate\Support\Collection
    {
        return collect([$this->starts_on])
            ->merge(collect($this->extra_dates ?? [])->map(fn ($d) => \Illuminate\Support\Carbon::parse($d)))
            ->sortBy(fn ($d) => $d->timestamp)->values();
    }

    /** Ближайший день проведения (или последний, если все прошли). */
    public function nextDay(): \Carbon\CarbonInterface
    {
        $days = $this->days();

        return $days->first(fn ($d) => $d->copy()->endOfDay()->isFuture()) ?? $days->last();
    }

    /** «14, 21, 28 ноября» или «28 ноября, 5 декабря». */
    public function daysLabel(): string
    {
        return $this->days()->map(fn ($d) => $d->copy()->locale(app()->getLocale()))
            ->groupBy(fn ($d) => $d->format('Y-m'))
            ->map(fn ($g) => $g->slice(0, -1)->map->format('j')->push($g->last()->translatedFormat('j F'))->implode(', ')) // «j F» — месяц в нужном падеже
            ->implode(', ');
    }

    /** «14 декабря» или «14–16 декабря» / «30 декабря – 2 января». */
    public function dateLabel(): string
    {
        if ($this->isRecurring()) {
            return $this->daysLabel();
        }
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
        if ($this->isRecurring()) {
            return $this->nextDay()->copy()->format('j');
        }
        $a = $this->starts_on->copy()->locale(app()->getLocale());
        $b = $this->ends_on?->copy()->locale(app()->getLocale());

        return ($b && ! $b->isSameDay($a)) ? $a->format('j').'–'.$b->format('j') : $a->format('j');
    }

    /** Подпись месяца на плашке: «ноябрь» или «ноябрь – декабрь». */
    public function monthBadge(): string
    {
        if ($this->isRecurring()) {
            return $this->nextDay()->copy()->locale(app()->getLocale())->translatedFormat('F');
        }
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
