<?php

namespace App\Models;

use App\Models\Concerns\HasSeo;
use App\Models\Concerns\HasTranslations;
use App\Support\Locales;
use Illuminate\Database\Eloquent\Model;

/**
 * Страница бренда, собранная из блоков (Filament Builder).
 * content = {"ru": [{type, data}, ...], "en": [...]} — у каждого языка свой набор блоков.
 */
class Page extends Model
{
    use HasSeo, HasTranslations;

    protected $guarded = ['id'];

    protected array $translatable = ['title', 'summary'];

    protected $casts = ['content' => 'array'];

    /** @return list<array{type: string, data: array}> */
    public function blocks(?string $locale = null): array
    {
        foreach (Locales::chain($locale ?? app()->getLocale()) as $code) {
            if (! empty($this->content[$code])) {
                return array_values($this->content[$code]);
            }
        }

        return [];
    }

    public function url(?string $locale = null): string
    {
        return route('page', ['locale' => $locale ?? app()->getLocale(), 'page' => $this->slug]);
    }

    /** Ссылка кнопки блока: booking, page:{slug} (другая страница сайта) или готовый адрес. */
    public static function resolveLink(?string $url): ?string
    {
        if (! $url) {
            return null;
        }
        if ($url === 'booking') {
            return route('booking');
        }
        if (str_starts_with($url, 'page:')) {
            return static::published()->where('slug', substr($url, 5))->first()?->url();
        }

        return $url;
    }
}
