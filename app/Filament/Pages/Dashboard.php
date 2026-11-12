<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

/** Главная админки: быстрые действия, цифры, что требует внимания. */
class Dashboard extends BaseDashboard
{
    protected static ?string $navigationLabel = 'Обзор';

    protected static ?string $title = 'Обзор сайта';

    public function getSubheading(): ?string
    {
        $user = auth()->user();

        return $user?->isEditor()
            ? 'Вы редактируете локацию «'.$user->hotel?->t('name').'». Ваши разделы — слева в меню.'
            : null;
    }

    public function getColumns(): int|array
    {
        return 1;
    }
}
