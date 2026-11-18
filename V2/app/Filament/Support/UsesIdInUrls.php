<?php

namespace App\Filament\Support;

use Illuminate\Database\Eloquent\Model;

/**
 * Модели сайта ищутся по слагу (getRouteKeyName() = 'slug'), а админка ищет записи по id
 * ($recordRouteKeyName = 'id', слаг может повторяться у разных отелей).
 * Чтобы адреса вида /admin/venues/restaurant/edit не вели на 404, в адреса админки
 * всегда подставляется id записи.
 */
trait UsesIdInUrls
{
    public static function getUrl(?string $name = null, array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?\Illuminate\Database\Eloquent\Model $tenant = null, bool $shouldGuessMissingParameters = false, ?string $configuration = null): string
    {
        if (($parameters['record'] ?? null) instanceof Model) {
            $parameters['record'] = $parameters['record']->getKey();
        }

        return parent::getUrl($name, $parameters, $isAbsolute, $panel, $tenant, $shouldGuessMissingParameters, $configuration);
    }
}
