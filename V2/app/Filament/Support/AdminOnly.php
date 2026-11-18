<?php

namespace App\Filament\Support;

/** Раздел админки только для администратора (редактору локации не виден и по прямой ссылке не открывается). */
trait AdminOnly
{
    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }
}
