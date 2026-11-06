<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

/**
 * Кэш готового HTML публичных страниц. Ключ включает «версию»: сброс кэша = смена версии
 * (работает с любым хранилищем кэша, в том числе файловым на shared-хостинге).
 */
class PageCache
{
    private const VERSION_KEY = 'pages.version';

    public static function enabled(): bool
    {
        return (bool) config('tenir.page_cache.enabled') && Setting::get('perf.page_cache', true) !== false;
    }

    public static function version(): string
    {
        return (string) Cache::rememberForever(self::VERSION_KEY, fn () => (string) microtime(true));
    }

    public static function flush(): void
    {
        Cache::forever(self::VERSION_KEY, (string) microtime(true));
    }

    public static function key(string $url): string
    {
        return 'page:'.self::version().':'.md5($url);
    }
}
