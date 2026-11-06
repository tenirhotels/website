<?php

namespace App\Models;

use App\Models\Concerns\HasSeo;
use App\Models\Concerns\HasTranslations;
use App\Support\Locales;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Experience extends Model
{
    use HasSeo, HasTranslations;

    /** Как гость оформляет впечатление: бронирование номера, запрос в отдел продаж или звонок/сообщение. */
    public const CTA = [
        'booking' => 'Бронирование номера',
        'sales' => 'Запрос в отдел продаж (корп. телефон и e-mail)',
        'contact' => 'Позвонить или написать (телефон, WhatsApp, e-mail бронирования)',
    ];

    protected $guarded = ['id'];

    protected array $translatable = ['title', 'summary', 'description', 'duration'];

    protected $casts = [
        'seasons' => 'array',
        'gallery' => 'array',
        'is_featured' => 'boolean',
    ];

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function categoryLabel(): ?string
    {
        $labels = config("tenir.experience_categories.{$this->category}");

        return $labels ? Locales::pick($labels) : null;
    }

    public function seasonLabels(): string
    {
        $selected = collect($this->seasons ?? []);
        // Отмечены все сезоны — значит круглый год.
        if ($selected->isNotEmpty() && collect(array_keys(config('tenir.seasons', [])))->diff($selected)->isEmpty()) {
            return __('site.common.all_year');
        }

        return $selected
            ->map(fn ($s) => Locales::pick(config("tenir.seasons.$s", [])))
            ->filter()
            ->implode(' · ');
    }

    public function url(?string $locale = null): string
    {
        return route('experiences.show', ['locale' => $locale ?? app()->getLocale(), 'experience' => $this->slug]);
    }
}
