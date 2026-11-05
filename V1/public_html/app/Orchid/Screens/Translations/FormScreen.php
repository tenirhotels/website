<?php

namespace App\Orchid\Screens\Translations;

use App\Models\Translation;
use App\Orchid\Screens\Abstraction\TranslationsScreen;
use Illuminate\Http\Request;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class FormScreen extends TranslationsScreen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Изменение';

    /**
     * Display header description.
     *
     * @var string
     */
    public $description = 'Редактирование перевода';

    /**
     * Query data.
     *
     * @param Translation $translation
     * @return array
     */
    public function query(Translation $translation): array
    {
        return [
            'translation' => $translation->toArray()
        ];
    }

    /**
     * Views.
     *
     * @return Layout[]
     */
    public function multiLanguageFields(): array
    {
        return [
            Input::make('translation.value')->title('Значение')
        ];
    }

    protected function singleLanguageFields(): array
    {
        return [
            Layout::rows([
                Input::make('translation.slug')
                    ->title('Ключ')
                    ->readonly()
            ])
        ];
    }


    public function save(Translation $translation, Request $request)
    {
        $translation->fill($request->input('translation'));
        $translation->save();

        Toast::success(__('Запрос успешно выполнен'));

        return redirect()->route('platform.translation.list');
    }
}
