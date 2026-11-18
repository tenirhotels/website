<?php

use App\Support\ImageVariants;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Storage;

/** Уменьшенные WebP-копии (800/1400) для всех уже загруженных фото. Нужен GD с WebP. */
return new class extends Migration
{
    public function up(): void
    {
        foreach (Storage::disk('public')->allFiles() as $path) {
            ImageVariants::backfill($path);
        }
    }

    public function down(): void {}
};
