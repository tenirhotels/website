<?php

namespace App\Orchid\Screens\Post;

use App\Models\Post;
use App\Orchid\Screens\Abstraction\TranslationsScreen;
use Illuminate\Http\Request;
use Orchid\Screen\Fields\Cropper;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Picture;
use Orchid\Screen\Fields\Quill;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class FormScreen extends TranslationsScreen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Пост';

    /**
     * Query data.
     *
     * @param Post $post
     * @return array
     */
    public function query(Post $post): array
    {
        $post->load('attachment');

        return [
            'post' => $post->toArray()
        ];
    }

    protected function multiLanguageFields(): array
    {
        return [
            Input::make('post.title')
                ->placeholder('Заголовок новости...')
                ->title('Заголовок'),
            Quill::make('post.content')
                ->placeholder('Контент...')
                ->title('Контент'),
        ];
    }

    protected function singleLanguageFields(): array
    {
        return [
            Layout::rows([
                Picture::make('post.photo')
                    ->targetUrl()
                    ->title('Фото'),
                Cropper::make('post.image')
                    ->targetId()
                    ->width(526)
                    ->height(304)
                    ->title('Миниатюра'),
                DateTimer::make('post.published_at')
                    ->title('Дата публикации')
                    ->placeholder('Дата публикации')
            ])
        ];
    }

    public function save(Post $post, Request $request)
    {
        $post->fill($request->input('post'));
        $post->save();

        if ($request->has('post.image')) {
            $post->attachment()->sync($request->input('post.image'));
        }


        Toast::info('Пост сохранен');

        return redirect()->route('platform.posts.list');
    }
}
