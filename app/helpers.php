<?php

use Illuminate\Support\Facades\Storage;

if (! function_exists('media')) {
    /** Публичный адрес загруженного файла (disk public) или внешней ссылки. */
    function media(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (str_starts_with($path, 'http')) {
            return $path;
        }

        // Относительный путь: работает на любом домене (Cloudflare, staging, локально).
        return '/'.ltrim(parse_url(Storage::disk('public')->url($path), PHP_URL_PATH), '/');
    }
}

if (! function_exists('media_srcset')) {
    /** srcset для фото, у которых есть уменьшенные копии (-800, -1400); иначе null. */
    function media_srcset(?string $path): ?string
    {
        if (blank($path) || str_starts_with($path, 'http') || ! str_ends_with($path, '.webp')) {
            return null;
        }

        static $cache = [];

        return $cache[$path] ??= (function () use ($path) {
            $disk = Storage::disk('public');
            $base = substr($path, 0, -5);
            $parts = [];
            foreach ([800, 1400] as $w) {
                if ($disk->exists("$base-$w.webp")) {
                    $parts[] = media("$base-$w.webp")." {$w}w";
                }
            }
            if (! $parts) {
                return null;
            }
            $size = @getimagesize($disk->path($path));
            $parts[] = media($path).' '.($size[0] ?? 2400).'w';

            return implode(', ', $parts);
        })();
    }
}

if (! function_exists('price')) {
    /** 125000 → «125 000» */
    function price(?int $amount): ?string
    {
        return $amount ? number_format($amount, 0, ',', ' ') : null;
    }
}
