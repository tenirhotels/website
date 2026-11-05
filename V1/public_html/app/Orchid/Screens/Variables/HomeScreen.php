<?php

namespace App\Orchid\Screens\Variables;

use App\Orchid\Screens\Abstraction\VariableScreen;
use Orchid\Screen\Fields\Cropper;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Fields\TextArea;
use Orchid\Support\Facades\Layout;

class HomeScreen extends VariableScreen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Главная страница';

    /**
     * Display header description.
     *
     * @var string
     */
    protected $variableKey = 'home';

    protected function multiLanguageFields(): array
    {
        return [
            Input::make('variable.data.header_title')
                ->placeholder('Заголовок...')
                ->title('Заголовок на главной странице'),

            Quill::make('variable.data.header_description')
                ->placeholder('Описание...')
                ->title('Описание на главной странице'),

            Input::make('variable.data.tenir_title')
                ->placeholder('Заголовок...')
                ->title('Заголовок для отеля Tenir'),

            Quill::make('variable.data.tenir_description')
                ->placeholder('Описание...')
                ->title('Описание об отеле Teni'),

            Input::make('variable.data.shymbulak_title')
                ->placeholder('Заголовок...')
                ->title('Заголовок для курорта Шымбулак'),

            Quill::make('variable.data.shymbulak_description')
                ->placeholder('Описание...')
                ->title('Описание об курорте Шымбулак'),
        ];
    }

    public function singleLanguageFields(): array
    {
        return [
            Layout::rows([
                Cropper::make('variable.data.banner_one')
                    ->placeholder('Баннер #1...')
                    ->title('Баннер #1'),
            ]),
            Layout::rows([
                Cropper::make('variable.data.banner_two')
                    ->placeholder('Баннер #2...')
                    ->title('Баннер #2'),
            ]),
        ];
    }
}
