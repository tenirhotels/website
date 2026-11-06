<?php

namespace Database\Seeders\Concerns;

use Illuminate\Support\Facades\Storage;

/** Копирует фото из database/seeders/media в storage/app/public/seed (фото из V1). */
trait CopiesMedia
{
    protected function media(string $file): ?string
    {
        $source = database_path("seeders/media/$file");
        if (! is_file($source)) {
            return null;
        }

        $path = "seed/$file";
        if (! Storage::disk('public')->exists($path)) {
            Storage::disk('public')->put($path, file_get_contents($source));
        }

        // уменьшенные копии для srcset
        foreach ([800, 1400] as $w) {
            $variant = preg_replace('/\.webp$/', "-$w.webp", $file);
            $vs = database_path("seeders/media/$variant");
            if (is_file($vs) && ! Storage::disk('public')->exists("seed/$variant")) {
                Storage::disk('public')->put("seed/$variant", file_get_contents($vs));
            }
        }

        return $path;
    }
}
