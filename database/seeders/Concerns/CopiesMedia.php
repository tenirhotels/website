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

        return $path;
    }
}
