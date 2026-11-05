<?php

namespace App\Orchid\Screens\HotelPrivilege;

use App\Models\HotelPrivilege;
use App\Orchid\Screens\Abstraction\TranslationsScreen;
use Illuminate\Http\Request;
use Orchid\Screen\Fields\Cropper;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Picture;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class FormScreen extends TranslationsScreen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Привелегии Отели';

    /**
     * Query data.
     *
     * @param HotelPrivilege $hotelPrivilege
     * @return array
     */
    public function query(HotelPrivilege $hotelPrivilege): array
    {
        $hotelPrivilege->load('attachment');

        return [
            'hotelPrivilege' => $hotelPrivilege->toArray()
        ];
    }

    protected function multiLanguageFields(): array
    {
        return [
            Input::make('hotelPrivilege.title')
                ->placeholder('Заголовок...')
                ->title('Заголовок'),
        ];
    }

    protected function singleLanguageFields(): array
    {
        return [
            Layout::rows([
                Cropper::make('hotelPrivilege.image')
                    ->width(320)
                    ->height(400)
                    ->targetId()
                    ->title('Фото'),
                Input::make('hotelPrivilege.position')
                    ->type('number')
                    ->min(0)
                    ->placeholder('Порядковый номер...')
                    ->title('Порядковый номер')
            ])
        ];
    }

    public function save(HotelPrivilege $hotelPrivilege, Request $request)
    {
        $hotelPrivilege->fill($request->input('hotelPrivilege'));
        $hotelPrivilege->save();

        if ($request->has('hotelPrivilege.image')) {
            $hotelPrivilege->attachment()->sync($request->input('hotelPrivilege.image'));
        }


        Toast::info('Привелегия сохранена');

        return redirect()->route('platform.hotel_privileges.list');
    }
}
