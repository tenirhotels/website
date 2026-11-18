<?php

namespace App\Console\Commands;

use App\Support\Gifts;
use Illuminate\Console\Command;

class SyncGifts extends Command
{
    protected $signature = 'site:sync-gifts';

    protected $description = 'Обновить список подарочных сертификатов из gifter (и сбросить кэш страниц, если он изменился)';

    public function handle(): int
    {
        $record = Gifts::sync();
        if ($record === null) {
            $this->warn('gifter не ответил или синхронизация выключена — остаётся прежняя копия.');

            return self::FAILURE;
        }
        $this->info(count($record['offers']).' сертификатов, отпечаток '.$record['hash']);

        return self::SUCCESS;
    }
}
