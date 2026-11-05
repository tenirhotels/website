<?php

namespace App\Orchid\Screens\Activity;

use App\Models\Activity;
use App\Orchid\Layouts\ActivitiesTable;
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
    public $name = 'Список активностей';

    /**
     * Query data.
     *
     * @return array
     */
    public function query(): array
    {
        return [
            'activities' => Activity::orderBy('position')->paginate()
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
                ->href(route('platform.activities.create')),
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
            ActivitiesTable::class
        ];
    }

    public function remove(Request $request)
    {
        Activity::whereId($request->input('id'))->delete();


        Toast::info('Активность удалена');

        return redirect()->route('platform.activities.list');
    }
}
