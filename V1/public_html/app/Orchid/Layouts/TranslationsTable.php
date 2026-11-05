<?php

namespace App\Orchid\Layouts;

use App\Models\Translation;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class TranslationsTable extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'translations';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): array
    {
        return [
            TD::set('name', __('Ключ'))
                ->width('40%')
                ->render(function (Translation $translation) {
                    return $translation->slug;
                }),
            TD::set('name', __('Название'))
                ->width('40%')
                ->render(function (Translation $translation) {
                    return $translation->value;
                }),

            TD::set('id', 'ID')
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(function (Translation $translation) {
                    return DropDown::make()
                        ->icon('options-vertical')
                        ->list([

                            Link::make(__('Изменить'))
                                ->route('platform.translation.form', $translation->id)
                                ->icon('icon-pencil'),
                        ]);
                }),
        ];
    }
}
