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
        if (blank($path) || str_starts_with($path, 'http') || ! preg_match('/^(.*)\.(webp|jpe?g|png)$/i', $path, $m) || preg_match('/-(800|1400)$/', $m[1])) {
            return null;
        }

        static $cache = [];
        $base0 = $m[1];

        return $cache[$path] ??= (function () use ($path, $base0) {
            $disk = Storage::disk('public');
            $base = $base0;
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

if (! function_exists('site_icon')) {
    /** Линейная иконка (24×24, цвет = currentColor). */
    function site_icon(string $name, string $class = 'h-6 w-6'): \Illuminate\Support\HtmlString
    {
        static $paths = [
            'size' => '<path d="M4 9V4h5M20 9V4h-5M4 15v5h5M20 15v5h-5"/>',
            'guests' => '<circle cx="9" cy="8" r="3.2"/><path d="M3 20c0-3.3 2.7-6 6-6s6 2.7 6 6"/><path d="M16 5.2a3 3 0 0 1 0 5.6M18 14.4c1.8.8 3 2.6 3 5.6"/>',
            'bed' => '<path d="M3 18V7M3 14h18v4M21 14v-2a3 3 0 0 0-3-3h-7v5"/><circle cx="7" cy="11" r="1.6"/>',
            'view' => '<path d="M2 19l6-9 4 5 3-4 7 8z"/><circle cx="17" cy="6" r="1.6"/>',
            'sauna' => '<path d="M8 4c-1.5 2 1.5 3 0 5M12 4c-1.5 2 1.5 3 0 5M16 4c-1.5 2 1.5 3 0 5M4 14h16v3a3 3 0 0 1-3 3H7a3 3 0 0 1-3-3z"/>',
            'window' => '<rect x="4" y="3" width="16" height="18" rx="1.5"/><path d="M12 3v18M4 12h16"/>',
            'ski' => '<path d="M3 20l9-16 9 16M7.5 13l4.5 2.5 4.5-2.5"/>',
            'module' => '<path d="M3 11l9-7 9 7v9H3z"/><path d="M10 20v-6h4v6"/>',
            'leaf' => '<path d="M5 19c0-8 5-14 15-14 0 10-6 15-14 15M5 19c2-4 5-7 9-9"/>',
            'wifi' => '<path d="M2.5 9a14 14 0 0 1 19 0M5.5 12.5a9.5 9.5 0 0 1 13 0M8.6 16a5 5 0 0 1 6.8 0"/><circle cx="12" cy="19" r="1" fill="currentColor"/>',
            'tv' => '<rect x="3" y="5" width="18" height="12" rx="1.5"/><path d="M8 21h8M12 17v4"/>',
            'coffee' => '<path d="M4 9h12v5a5 5 0 0 1-5 5H9a5 5 0 0 1-5-5zM16 10h1.5a2.5 2.5 0 0 1 0 5H16M8 3c-1 1.2 1 2 0 3.5M12 3c-1 1.2 1 2 0 3.5"/>',
            'minibar' => '<rect x="5" y="3" width="14" height="18" rx="1.5"/><path d="M5 10h14M8 6v2M8 13v3"/>',
            'bath' => '<path d="M3 12h18v2a5 5 0 0 1-5 5H8a5 5 0 0 1-5-5zM6 12V6a2 2 0 0 1 3.5-1.3M7 19l-1 2M17 19l1 2"/>',
            'locker' => '<rect x="4" y="3" width="16" height="18" rx="1.5"/><path d="M12 3v18M4 12h16M9 7.5v1M15 15.5v1"/>',
            'robe' => '<path d="M8 3l4 4 4-4 3 4-2 3v11H7V10L5 7zM12 7v14"/>',
            'car' => '<path d="M4 16v-4l2-5h12l2 5v4M3 16h18M7.5 19a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3zM16.5 19a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3zM4 12h16"/>',
            'cable' => '<path d="M3 5l18 5M12 7.5V12M7 12h10v6a1 1 0 0 1-1 1H8a1 1 0 0 1-1-1z"/>',
            'heat' => '<path d="M7 20V6a3 3 0 0 1 6 0v14M10 9h.01M17 7c-1.5 2 1.5 3 0 5s1.5 3 0 5"/>',
        ];

        return new \Illuminate\Support\HtmlString('<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" class="'.e($class).'" aria-hidden="true">'.($paths[$name] ?? $paths['leaf']).'</svg>');
    }
}

if (! function_exists('amenity_icon')) {
    /** Подбирает иконку по тексту удобства. */
    function amenity_icon(string $text, string $class = 'h-6 w-6'): \Illuminate\Support\HtmlString
    {
        $t = mb_strtolower($text);
        $has = fn (array $words) => collect($words)->contains(fn ($w) => str_contains($t, $w));
        $name = match (true) {
            $has(['wi-fi', 'wifi', 'вай']) => 'wifi',
            $has(['телевиз', 'спутник', ' tv', 'tv ', 'теледидар']) => 'tv',
            $has(['саун', 'sauna']) => 'sauna',
            $has(['мини-бар', 'минибар', 'minibar', 'mini-bar']) => 'minibar',
            $has(['кофе', 'чайник', 'coffee', 'kettle', 'завтрак', 'breakfast', 'таңғы']) => 'coffee',
            $has(['кроват', 'bed', 'төсек']) => 'bed',
            $has(['халат', 'тапоч', 'robe', 'slipper']) => 'robe',
            $has(['душ', 'ванн', 'shower', 'bath', 'туалет', 'toiletr', 'фен', 'hairdryer', 'hair dryer']) => 'bath',
            $has(['локер', 'locker', 'хранен', 'storage']) => 'locker',
            $has(['окн', 'window', 'терез', 'терас']) => 'window',
            $has(['ski', 'лыж']) => 'ski',
            $has(['модул', 'module']) => 'module',
            $has(['климат', 'отоплен', 'тёплый пол', 'heating', 'climate']) => 'heat',
            default => 'leaf',
        };

        return site_icon($name, $class);
    }
}

if (! function_exists('price')) {
    /** 125000 → «125 000» */
    function price(?int $amount): ?string
    {
        return $amount ? number_format($amount, 0, ',', ' ') : null;
    }
}
