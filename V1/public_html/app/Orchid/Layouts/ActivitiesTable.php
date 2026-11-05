<?php

namespace App\Orchid\Layouts;

use App\Models\Activity;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class ActivitiesTable extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'activities';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): array
    {
        return [
            TD::set('image', __('Изображение'))
                ->cantHide()
                ->width(130)
                ->render(function (Activity $activity) {
                    return '<img style="width: 120px" src="'.$activity->image_url.'">';
                }),
            TD::set('name', __('Name'))
                ->sort()
                ->cantHide()
                ->filter(TD::FILTER_TEXT)
                ->render(function (Activity $activity) {
                    return $activity->title;
                }),

            TD::set('id', 'ID')
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(function (Activity $activity) {
                    return DropDown::make()
                        ->icon('options-vertical')
                        ->list([

                            Link::make(__('Edit'))
                                ->route('platform.activities.edit', $activity->id)
                                ->icon('icon-pencil'),

                            Button::make(__('Delete'))
                                ->method('remove')
                                ->confirm('Вы действительно хотите удалить активность?')
                                ->parameters([
                                    'id' => $activity->id,
                                ])
                                ->icon('icon-trash'),
                        ]);
                }),
        ];
    }
}
