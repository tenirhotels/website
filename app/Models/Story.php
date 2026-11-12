<?php

namespace App\Models;

use App\Models\Concerns\HasSeo;
use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Story extends Model
{
    use \App\Models\Concerns\ForcesEditorHotel;
    use HasSeo, HasTranslations;

    protected $guarded = ['id'];

    protected array $translatable = ['title', 'summary', 'body'];

    protected $casts = ['published_at' => 'datetime'];

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function scopeLive(Builder $query): Builder
    {
        return $query->published()->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    public function url(?string $locale = null): string
    {
        return route('stories.show', ['locale' => $locale ?? app()->getLocale(), 'story' => $this->slug]);
    }
}
