<?php

use App\Models\RoomType;
use Database\Seeders\Concerns\RoomContent;
use Illuminate\Database\Migrations\Migration;

/** Описание, кровать, вместимость и удобства Deluxe — из текущего сайта (V1). Не трогает то, что уже правили вручную. */
return new class extends Migration
{
    public function up(): void
    {
        $room = RoomType::where('slug', 'deluxe-mountain-view')->first();
        if (! $room) {
            return;
        }

        $amenities = $room->amenities ?? [];
        if (($amenities['ru'] ?? []) === RoomContent::OLD_AMENITIES_RU) {
            $room->amenities = ['ru' => RoomContent::amenities('ru'), 'en' => RoomContent::amenities('en')];
        }

        $description = $room->description ?? [];
        foreach (['ru', 'en'] as $l) {
            if (str_contains((string) ($description[$l] ?? ''), '[')) {
                $description[$l] = RoomContent::description($l);
            }
        }
        $room->description = $description;

        if (blank($room->bed)) {
            $room->bed = ['ru' => 'King-size', 'en' => 'King-size'];
        }
        if (! $room->max_guests) {
            $room->max_guests = 2;
        }
        $room->save();
    }

    public function down(): void {}
};
