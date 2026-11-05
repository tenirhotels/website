<?php

namespace App\Orchid\Screens\Variables;

use App\Orchid\Screens\Abstraction\VariableScreen;
use Orchid\Screen\Fields\Input;

class HotelServiceScreen extends VariableScreen
{
    public $name = 'Отель: Дополнительные сервисы';

    protected $variableKey = 'hotel-services';


    protected function multiLanguageFields(): array
    {
        return [
            Input::make('variable.data.title_rent')->title('Заголовок для "Аренда горнолыжного и сноубордического инвентаря"')
                ->value('Аренда горнолыжного и сноубордического инвентаря'),

            Input::make('variable.data.title_rental')->title('Заголовок для "Прокат горнолыжной одежды"')
                ->value('Прокат горнолыжной одежды'),

            Input::make('variable.data.title_taxi')->title('Заголовок для "Ратрак-такси доставит вас к экоотелю даже вне работы канатной дороги"')
                ->value('Ратрак-такси доставит вас к экоотелю даже вне работы канатной дороги'),

            Input::make('variable.data.title_delivery')->title('Заголовок для "Доставка еды в номер из рестобара Oblaka согласно вашим предпочтениям"')
                ->value('Доставка еды в номер из рестобара Oblaka согласно вашим предпочтениям'),
        ];
    }

}
