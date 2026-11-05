<?php

namespace App\Orchid\Screens\Variables;

use App\Orchid\Screens\Abstraction\VariableScreen;
use Orchid\Screen\Fields\Cropper;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Label;
use Orchid\Screen\Fields\Picture;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\Upload;
use Orchid\Support\Facades\Layout;

class SettingsScreen extends VariableScreen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Настройки';


    protected $variableKey = 'settings';

    protected function singleLanguageFields(): array
    {
        return [
            Layout::rows([
                Input::make('variable.data.phone')
                    ->placeholder('Номер телефона...')
                    ->title('Номер телефона'),
                Input::make('variable.data.instagram')
                    ->placeholder('Инстаграм...')
                    ->title('Инстаграм'),
                Input::make('variable.data.facebook')
                    ->placeholder('Facebook...')
                    ->title('Facebook'),
                //SEO
                Label::make('meta')
                    ->title('SEO мета теги'),
                Input::make('variable.data.seo_title')
                    ->placeholder('Заголовок...')
                    ->title('Заголовок'),
                TextArea::make('variable.data.seo_description')
                    ->placeholder('Описание...')
                    ->title('Описание'),
                TextArea::make('variable.data.seo_keywords')
                    ->placeholder('Перечислите ключевые слова через запятую...')
                    ->title('Ключевые слова'),
                Cropper::make('variable.data.news_list_header')
                    ->placeholder('Баннер для списка новостей...')
                    ->title('Баннер для списка новостей'),
                Cropper::make('variable.data.activities')
                    ->placeholder('Баннер для активностей...')
                    ->title('Баннер для активностей'),
            ])
        ];
    }

    protected function multiLanguageFields(): array
    {
        return [
            Input::make('variable.data.address')
                ->placeholder('Адрес...')
                ->title('Адрес'),
            Upload::make('variable.data.offer')
                ->maxFileSize(1)
                ->placeholder('Оферта...')
                ->title('Оферта'),
        ];
    }
}
