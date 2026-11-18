<?php

use App\Models\RoomType;
use Illuminate\Database\Migrations\Migration;

/** Возраст ребёнка на дополнительном месте: «от 6 до 14 лет» (было «с 8 лет»). Меняет только текст, совпадающий со старым дословно. */
return new class extends Migration
{
    public function up(): void
    {
        $map = [
            'ru' => ['для детей с 8 лет', 'для детей от 6 до 14 лет'],
            'en' => ['for children from 8 years old', 'for children aged 6 to 14'],
        ];
        foreach (RoomType::all() as $room) {
            $d = (array) $room->getRawOriginal('description') ? json_decode($room->getRawOriginal('description'), true) : [];
            $changed = false;
            foreach ($map as $l => [$old, $new]) {
                if (isset($d[$l]) && str_contains($d[$l], $old)) {
                    $d[$l] = str_replace($old, $new, $d[$l]);
                    $changed = true;
                }
            }
            if ($changed) {
                $room->description = $d;
                $room->saveQuietly();
            }
        }
    }

    public function down(): void {}
};
