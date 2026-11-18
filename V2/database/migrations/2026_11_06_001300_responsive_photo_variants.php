<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Storage;

/** Копирует уменьшенные копии фото (-800, -1400) в storage для srcset. */
return new class extends Migration
{
    public function up(): void
    {
        foreach (glob(database_path('seeders/media/tenir-*-{800,1400}.webp'), GLOB_BRACE) as $file) {
            $name = basename($file);
            if (! Storage::disk('public')->exists("seed/$name")) {
                Storage::disk('public')->put("seed/$name", file_get_contents($file));
            }
        }
    }

    public function down(): void {}
};
