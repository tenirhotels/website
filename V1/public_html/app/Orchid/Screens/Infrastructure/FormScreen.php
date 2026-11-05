<?php

namespace App\Orchid\Screens\Infrastructure;

use App\Models\Infrastructure;
use App\Orchid\Screens\Abstraction\TranslationsScreen;
use Illuminate\Http\Request;
use Orchid\Screen\Fields\Cropper;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Picture;
use Orchid\Screen\Fields\Upload;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class FormScreen extends TranslationsScreen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Инфраструктура Отели';

    /**
     * Display header description.
     *
     * @var string
     */
    public $description = 'FormScreen';

    /**
     * Query data.
     *
     * @param Infrastructure $infrastructure
     * @return array
     */
    public function query(Infrastructure $infrastructure): array
    {
        $infrastructure->load('attachment');

        return [
            'infrastructure' => $infrastructure->toArray()
        ];
    }

    protected function multiLanguageFields(): array
    {
        return [
            Input::make('infrastructure.title')
                ->placeholder('Заголовок...')
                ->title('Заголовок'),

            Input::make('infrastructure.content')
                ->placeholder('Описание...')
                ->title('Описание'),
        ];
    }

    protected function singleLanguageFields(): array
    {
        return [
            Layout::rows([
                Cropper::make('infrastructure.image')
                    ->width(680)
                    ->height(437)
                    ->targetId()
                    ->title('Фото'),
                Upload::make('infrastructure.attachment')
                    ->title('Список фотографий'),
                Input::make('activity.position')
                    ->type('number')
                    ->min(0)
                    ->placeholder('Порядковый номер...')
                    ->title('Порядковый номер')
            ])
        ];
    }

    public function save(Infrastructure $infrastructure, Request $request)
    {
        $infrastructure->fill($request->input('infrastructure'));
        $infrastructure->save();

        if ($request->has('infrastructure.attachment')) {
            $infrastructure->attachment()->sync($request->input('infrastructure.attachment'));
        }


        Toast::info('Инфаструктура сохранена');

        return redirect()->route('platform.infrastructures.list');
    }
}
