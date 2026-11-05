<?php

namespace App\Orchid\Screens\HotelPrivilege;

use App\Models\HotelPrivilege;
use App\Orchid\Layouts\HotelPrivilegesTable;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Toast;

class ListScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Список привелегии отели';


    /**
     * Query data.
     *
     * @return array
     */
    public function query(): array
    {
        return [
            'hotelPrivileges' => HotelPrivilege::orderBy('position')->paginate()
        ];
    }

    /**
     * Button commands.
     *
     * @return Action[]
     */
    public function commandBar(): array
    {
        return [
            Link::make(__('Add'))
                ->icon('icon-plus')
                ->href(route('platform.hotel_privileges.create')),
        ];
    }

    /**
     * Views.
     *
     * @return Layout[]
     */
    public function layout(): array
    {
        return [
            HotelPrivilegesTable::class
        ];
    }

    public function remove(Request $request)
    {
        HotelPrivilege::whereId($request->input('id'))->delete();

        Toast::info('Привелегия удалена');

        return redirect()->route('platform.hotel_privileges.list');
    }
}
