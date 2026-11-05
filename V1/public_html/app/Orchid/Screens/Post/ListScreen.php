<?php

namespace App\Orchid\Screens\Post;

use App\Models\Post;
use App\Orchid\Layouts\PostsTable;
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
    public $name = 'Список новостей';

    /**
     * Query data.
     *
     * @return array
     */
    public function query(): array
    {
        return [
            'posts' => Post::orderBy('published_at')->paginate()
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
                ->href(route('platform.posts.create')),
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
            PostsTable::class
        ];
    }

    public function remove(Request $request)
    {
        Post::whereId($request->input('id'))->delete();


        Toast::info('Пост удален');

        return redirect()->route('platform.posts.list');
    }
}
