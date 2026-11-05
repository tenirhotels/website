<?php

namespace App\Orchid\Screens\Activity;

use App\Models\Activity;
use App\Orchid\Screens\Abstraction\TranslationsScreen;
use Illuminate\Http\Request;
use Orchid\Screen\Fields\Cropper;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
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
    public $name = 'Активность';

    /**
     * Query data.
     *
     * @param Activity $activity
     * @return array
     */
    public function query(Activity $activity): array
    {
        $activity->load('attachment');

        return [
            'activity' => $activity->toArray()
        ];
    }

    protected function multiLanguageFields(): array
    {
        return [
            Input::make('activity.title')
                ->placeholder('Заголовок активности...')
                ->title('Заголовок'),
            Quill::make('activity.content')
                ->placeholder('Описание активности...')
                ->title('Описание'),
        ];
    }

    protected function singleLanguageFields(): array
    {
        return [
            Layout::rows([
                Cropper::make('activity.image')
                    ->targetId()
                    ->width(258)
                    ->height(346)
                    ->groups('main')
                    ->title('Фото'),
                Upload::make('activity.images')
                    ->groups('images')
                    ->title('Слайдер'),
                Input::make('activity.position')
                    ->type('number')
                    ->min(0)
                    ->placeholder('Порядковый номер...')
                    ->title('Порядковый номер')
            ])
        ];
    }

    public function save(Activity $activity, Request $request)
    {
        $activity->fill($request->input('activity'));
        $activity->save();

        if ($request->has('activity.image')) {
            $activity->attachment()->sync([$request->input('activity.image')]);
        }

        Toast::info('Активность сохранена');

        return redirect()->route('platform.activities.list');
    }
}
