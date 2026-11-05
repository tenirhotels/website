<?php

namespace App\Orchid\Screens\Variables;

use App\Orchid\Screens\Abstraction\VariableScreen;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Matrix;
use Orchid\Screen\Screen;

class HotelFacilityScreen extends VariableScreen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Отель: Удобства';

    protected $variableKey = 'hotel-facilities';

    protected function multiLanguageFields(): array
    {
        return [
            Input::make('variable.data.title_media')->title('Заголовок для "Медиа и технологии"')->value('Медиа и технологии'),
            Matrix::make('variable.data.list_media')
                ->columns([
                    'Текст' => 'list'
                ])->fields([
                    'list' => Input::make('list')->title('Текст'),
                ])->title('Перечислите услуги'),

            Input::make('variable.data.title_foods')->title('Заголовок для "Еда и напитки"')->value('Еда и напитки'),
            Matrix::make('variable.data.list_foods')
                ->columns([
                    'Текст' => 'list'
                ])->fields([
                    'list' => Input::make('list')->title('Текст'),
                ])->title('Перечислите услуги'),

            Input::make('variable.data.title_bedroom')->title('Заголовок для "Спальня"')->value('Спальня'),
            Matrix::make('variable.data.list_bedroom')
                ->columns([
                    'Текст' => 'list'
                ])->fields([
                    'list' => Input::make('list')->title('Текст'),
                ])->title('Перечислите услуги'),

            Input::make('variable.data.title_bathroom')->title('Заголовок для "Ванная комната"')->value('Ванная комната'),
            Matrix::make('variable.data.list_bathroom')
                ->columns([
                    'Текст' => 'list'
                ])->fields([
                    'list' => Input::make('list')->title('Текст'),
                ])->title('Перечислите услуги'),

            Input::make('variable.data.title_sauna')->title('Заголовок для "Cауна"')->value('Cауна'),
            Matrix::make('variable.data.list_sauna')
                ->columns([
                    'Текст' => 'list'
                ])->fields([
                    'list' => Input::make('list')->title('Текст'),
                ])->title('Перечислите услуги'),

            Input::make('variable.data.title_locker')->title('Заголовок для "Локер"')->value('Локер'),
            Matrix::make('variable.data.list_locker')
                ->columns([
                    'Текст' => 'list'
                ])->fields([
                    'list' => Input::make('list')->title('Текст'),
                ])->title('Перечислите услуги'),
        ];
    }
}
