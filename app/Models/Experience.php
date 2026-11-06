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
        return collect($this->seasons ?? [])
            ->map(fn ($s) => Locales::pick(config("tenir.seasons.$s", [])))
            ->filter()
            ->implode(' · ');
    }

    public function url(?string $locale = null): string
    {
        return route('experiences.show', ['locale' => $locale ?? app()->getLocale(), 'experience' => $this->slug]);
    }
}
