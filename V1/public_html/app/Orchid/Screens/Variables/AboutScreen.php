<?php

namespace App\Orchid\Screens\Variables;

use App\Orchid\Screens\Abstraction\VariableScreen;
use Orchid\Screen\Fields\Cropper;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class AboutScreen extends VariableScreen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Наша философия';

    protected $variableKey = 'about';


    protected function multiLanguageFields(): array
    {
        return [
            Input::make('variable.data.title_one')
                ->placeholder('Заголовок #1...')
                ->title('Заголовок #1'),

            Quill::make('variable.data.content_one')
                ->placeholder('Описание #1...')
                ->title('Описание #1'),

            Input::make('variable.data.title_two')
                ->placeholder('Заголовок #2...')
                ->title('Заголовок #2'),

            Quill::make('variable.data.content_two')
                ->placeholder('Описание #2...')
                ->title('Описание #2'),

            Input::make('variable.data.last_title')
                ->placeholder('Текст над иконками...')
                ->title('Текст над иконками'),
        ];
    }

    public function singleLanguageFields(): array
    {
        return [
            Layout::rows([
                Cropper::make('variable.data.header')
                    ->placeholder('Баннер...')
                    ->title('Баннер'),
            ])
        ];
    }
}
