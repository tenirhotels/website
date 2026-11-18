<?php

namespace App\Console\Commands;

use App\Support\ImageVariants;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ImagesVariants extends Command
{
    protected $signature = 'images:variants';

    protected $description = 'Создать уменьшенные WebP-копии (800/1400) для уже загруженных фото';

    public function handle(): int
    {
        $n = 0;
        foreach (Storage::disk('public')->allFiles() as $path) {
            $n += ImageVariants::backfill($path);
        }
        $this->info("Создано копий: $n");

        return self::SUCCESS;
    }
}
