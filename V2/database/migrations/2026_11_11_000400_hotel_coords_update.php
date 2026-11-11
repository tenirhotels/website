<?php

use App\Models\Location;
use Illuminate\Database\Migrations\Migration;

/** Уточнённые координаты Tenir Hotel Shymbulak. Меняем только если там ещё прежние значения (правки редактора не трогаем). */
return new class extends Migration
{
    public function up(): void
    {
        Location::query()->where('slug', 'shymbulak')->get()->each(function (Location $l) {
            if (abs((float) $l->latitude - 43.113991) < 1e-6 && abs((float) $l->longitude - 77.111328) < 1e-6) {
                $l->update(['latitude' => 43.113893, 'longitude' => 77.111801]);
            }
        });
    }

    public function down(): void {}
};
