<?php

namespace App\Console\Commands;

use App\Support\PageCache;
use Illuminate\Console\Command;

class FlushPageCache extends Command
{
    protected $signature = 'site:flush-cache';

    protected $description = 'Сбросить кэш готовых страниц сайта';

    public function handle(): int
    {
        PageCache::flush();
        $this->info('Кэш страниц сброшен.');

        return self::SUCCESS;
    }
}
