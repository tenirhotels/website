<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

/** Главная админки: быстрые действия, цифры, что требует внимания. */
class Dashboard extends BaseDashboard
{
    protected static ?string $navigationLabel = 'Обзор';

    protected static ?string $title = 'Обзор сайта';

    public function getColumns(): int|array
    {
        return 1;
    }
}
