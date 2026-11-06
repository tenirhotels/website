<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/** Признание: награды, публикации в СМИ, рейтинги. Показывается на главной и в разметке schema.org. */
class Recognition extends Model
{
    use HasTranslations;

    protected $guarded = ['id'];

    protected array $translatable = ['title', 'subtitle'];

    protected $casts = ['is_published' => 'boolean', 'show_on_home' => 'boolean'];

    public const KINDS = ['award' => 'Награда', 'press' => 'СМИ', 'rating' => 'Рейтинг'];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)->orderBy('sort')->orderByDesc('year');
    }

    /** @return \Illuminate\Support\Collection<int, self> */
    public static function forHome()
    {
        try {
            return self::published()->where('show_on_home', true)->get();
        } catch (\Throwable) {
            return collect(); // до миграций
        }
    }
}
