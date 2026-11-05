<?php

namespace App\Orchid\Screens\Infrastructure;

use App\Models\HotelPrivilege;
use App\Models\Infrastructure;
use App\Orchid\Layouts\InfrastructureTable;
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
    public $name = 'Список инфраструктур отели';

    /**
     * Display header description.
     *
     * @var string
     */
    public $description = 'ListScreen';

    /**
     * Query data.
     *
     * @return array
     */
    public function query(): array
    {
        return [
            'infrastructures' => Infrastructure::orderBy('position')->paginate()
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
                ->href(route('platform.infrastructures.create')),
        ];
    }

    public function layout(): array
    {
        return [
            InfrastructureTable::class
        ];
    }

    public function remove(Request $request)
    {
        Infrastructure::whereId($request->input('id'))->delete();

        Toast::info('Инфраструктура удалена');

        return redirect()->route('platform.infrastructures.list');
    }
}
