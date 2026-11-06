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

if (! function_exists('price')) {
    /** 125000 → «125 000» */
    function price(?int $amount): ?string
    {
        return $amount ? number_format($amount, 0, ',', ' ') : null;
    }
}
