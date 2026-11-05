<?php

namespace App\Orchid\Layouts;

use App\Models\Post;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class PostsTable extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'posts';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): array
    {
        return [
            TD::set('name', __('Name'))
                ->sort()
                ->cantHide()
                ->filter(TD::FILTER_TEXT)
                ->render(function (Post $post) {
                    return $post->title;
                }),
            TD::set('date', 'Дата публикации')
                ->sort()
                ->cantHide()
                ->filter(TD::FILTER_TEXT)
                ->render(function (Post $post) {
                    return $post->published_at;
                }),

            TD::set('id', 'ID')
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(function (Post $post) {
                    return DropDown::make()
                        ->icon('options-vertical')
                        ->list([

                            Link::make(__('Edit'))
                                ->route('platform.posts.edit', $post->id)
                                ->icon('icon-pencil'),

                            Button::make(__('Delete'))
                                ->method('remove')
                                ->confirm('Вы действительно хотите удалить пост?')
                                ->parameters([
                                    'id' => $post->id,
                                ])
                                ->icon('icon-trash'),
                        ]);
                }),
        ];
    }
}
