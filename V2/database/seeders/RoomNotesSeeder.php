<?php

namespace Database\Seeders;

use App\Models\RoomType;
use Illuminate\Database\Seeder;

/** Черновик «Важно знать» для Deluxe (условия из описания в Exely). Заполняет только пустое поле. */
class RoomNotesSeeder extends Seeder
{
    public function run(): void
    {
        $room = RoomType::where('slug', 'deluxe-mountain-view')->first();
        if (! $room || filled($room->notes)) {
            return;
        }
        $room->notes = [
            'ru' => '<ul><li>Отель расположен высоко в горах (3 200 м над уровнем моря): чтобы избежать неблагоприятного воздействия на организм, дети младше 6 лет не размещаются.</li><li>Курение запрещено.</li><li>Размещение с животными невозможно.</li></ul>',
            'en' => '<ul><li>The hotel stands at 3,200 metres above sea level. To avoid undesirable effects on the body, children under 6 years of age are not allowed.</li><li>No smoking.</li><li>No pets.</li></ul>',
        ];
        $room->save();
    }
}
