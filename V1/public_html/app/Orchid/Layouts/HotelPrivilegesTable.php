<?php

namespace App\Orchid\Layouts;

use App\Models\Activity;
use App\Models\HotelPrivilege;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class HotelPrivilegesTable extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'hotelPrivileges';

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
                ->render(function (HotelPrivilege $hotelPrivilege) {
                    return '<img style="width: 120px" src="'.$hotelPrivilege->image_url.'">';
                }),
            TD::set('name', __('Name'))
                ->sort()
                ->cantHide()
                ->filter(TD::FILTER_TEXT)
                ->render(function (HotelPrivilege $hotelPrivilege) {
                    return $hotelPrivilege->title;
                }),

            TD::set('id', 'ID')
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(function (HotelPrivilege $hotelPrivilege) {
                    return DropDown::make()
                        ->icon('options-vertical')
                        ->list([

                            Link::make(__('Edit'))
                                ->route('platform.hotel_privileges.edit', $hotelPrivilege->id)
                                ->icon('icon-pencil'),

                            Button::make(__('Delete'))
                                ->method('remove')
                                ->confirm('Вы действительно хотите удалить активность?')
                                ->parameters([
                                    'id' => $hotelPrivilege->id,
                                ])
                                ->icon('icon-trash'),
                        ]);
                }),
        ];
    }

}
