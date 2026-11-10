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
        $chain = Locales::chain($locale ?? app()->getLocale());
        foreach ($chain as $code) {
            if (! empty($this->content[$code])) {
                return $this->withSharedMedia(array_values($this->content[$code]), $code, $chain);
            }
        }

        return [];
    }

    /** Картинка блока одна на все языки: если у этого языка она не загружена, берём её у такого же блока другого языка. */
    private function withSharedMedia(array $blocks, string $own, array $chain): array
    {
        $others = array_values(array_filter(array_unique([...$chain, ...array_keys((array) $this->content)]), fn ($c) => $c !== $own));
        foreach ($blocks as $i => $block) {
            foreach (['image', 'video'] as $key) {
                if (array_key_exists($key, $block['data'] ?? []) && blank($block['data'][$key])) {
                    foreach ($others as $code) {
                        $twin = $this->content[$code][$i] ?? null;
                        if ($twin && ($twin['type'] ?? null) === ($block['type'] ?? null) && filled($twin['data'][$key] ?? null)) {
                            $blocks[$i]['data'][$key] = $twin['data'][$key];
                            break;
                        }
                    }
                }
            }
        }

        return $blocks;
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
