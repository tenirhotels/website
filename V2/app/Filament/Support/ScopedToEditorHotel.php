<?php

namespace App\Filament\Support;

use Illuminate\Database\Eloquent\Builder;

/** Редактор локации видит и правит только записи своего отеля; администратор — всё. */
trait ScopedToEditorHotel
{
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        return $user instanceof \App\Models\User && $user->isEditor()
            ? $query->where($query->getModel()->getTable().'.hotel_id', $user->hotel_id)
            : $query;
    }
}
