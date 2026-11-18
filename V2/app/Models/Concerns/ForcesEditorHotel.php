<?php

namespace App\Models\Concerns;

/**
 * Редактор локации не может создать запись для чужого отеля или «для всех»,
 * и не может перенести существующую: при сохранении локация подставляется из его профиля.
 */
trait ForcesEditorHotel
{
    public static function bootForcesEditorHotel(): void
    {
        static::saving(function ($model) {
            $user = auth()->user();
            if ($user instanceof \App\Models\User && $user->isEditor()) {
                $model->hotel_id = $user->hotel_id;
            }
        });
    }
}
