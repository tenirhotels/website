<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/** Загруженные фото → WebP (до 2400px) + уменьшенные копии -800 и -1400 для srcset. */
class ImageVariants
{
    public const WIDTHS = [800, 1400];

    public static function supported(): bool
    {
        return function_exists('imagewebp') && function_exists('imagecreatefromstring');
    }

    /** Сохраняет загруженный файл; возвращает путь на диске public. */
    public static function store($file, string $directory): string
    {
        $raw = @file_get_contents($file->getRealPath());
        $mime = (string) $file->getMimeType();

        if (! self::supported() || ! $raw || ! str_starts_with($mime, 'image/') || in_array($mime, ['image/svg+xml', 'image/gif'], true)) {
            return $file->storePublicly($directory, 'public');
        }

        $img = @imagecreatefromstring($raw);
        if (! $img) {
            return $file->storePublicly($directory, 'public');
        }

        $base = trim($directory, '/').'/'.Str::random(24);
        imagepalettetotruecolor($img);
        imagealphablending($img, true);
        imagesavealpha($img, true);

        self::write($img, "$base.webp", 2400);
        foreach (self::WIDTHS as $w) {
            self::write($img, "$base-$w.webp", $w);
        }
        imagedestroy($img);

        return "$base.webp";
    }

    /** Создаёт недостающие копии для уже лежащего на диске файла. */
    public static function backfill(string $path): int
    {
        $disk = Storage::disk('public');
        if (! self::supported() || ! preg_match('/^(.*)\.(webp|jpe?g|png)$/i', $path, $m) || preg_match('/-(800|1400)$/', $m[1])) {
            return 0;
        }
        $made = 0;
        $img = null;
        foreach (self::WIDTHS as $w) {
            if ($disk->exists("{$m[1]}-$w.webp")) {
                continue;
            }
            $img ??= @imagecreatefromstring($disk->get($path));
            if (! $img) {
                return $made;
            }
            self::write($img, "{$m[1]}-$w.webp", $w);
            $made++;
        }

        return $made;
    }

    private static function write($img, string $target, int $maxWidth): void
    {
        $w = imagesx($img);
        $h = imagesy($img);
        if ($w > $maxWidth) {
            $img = imagescale($img, $maxWidth, (int) round($h * $maxWidth / $w), IMG_BICUBIC);
        }
        ob_start();
        imagewebp($img, null, 78);
        Storage::disk('public')->put($target, ob_get_clean());
    }
}
