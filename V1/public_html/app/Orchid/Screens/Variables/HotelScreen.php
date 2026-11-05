<?php

namespace App\Orchid\Screens\Variables;

use App\Orchid\Screens\Abstraction\VariableScreen;
use Orchid\Screen\Fields\Cropper;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Label;
use Orchid\Screen\Fields\Matrix;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Fields\Upload;
use Orchid\Support\Facades\Layout;

class HotelScreen extends VariableScreen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Отель';

    protected $variableKey = 'hotel';

    protected function multiLanguageFields(): array
    {
        return [
            Quill::make('variable.data.description')
                ->placeholder('Описание...')
                ->title('Описание'),

            Label::make('title')->title('Особенности проживания'),

            Quill::make('variable.data.how_to_get')
                ->placeholder('Как добраться...')
                ->title('Как добраться'),

            Input::make('variable.data.number')->title('Описание для номера отеля'),

            Upload::make('variable.data.banner')
                ->title('Баннер номеров1')
                ->groups('hotel-banners'),


            Matrix::make('variable.data.schedules')
                ->columns([
                    'Текст' => 'content'
                ])
                ->fields([
                    'content' => Input::make('content')->title('Текст'),
                ])
                ->title('Остановки и время работы')

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
