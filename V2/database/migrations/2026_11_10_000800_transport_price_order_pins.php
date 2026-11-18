<?php

use App\Support\LocationMap;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Транспорт: цена, кнопка «Заказать» (контакты отеля), привязка к точкам на карте.
 * Карте Шымбулака добавляем зоны высадки у Медеу и у станции Шымбулак.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transports', function (Blueprint $table) {
            $table->json('price')->nullable()->after('hours');
            $table->boolean('orderable')->default(true)->after('image');
            $table->json('map_pins')->nullable()->after('orderable');
        });

        foreach (DB::table('locations')->whereNotNull('map')->get(['id', 'map']) as $loc) {
            $map = json_decode($loc->map, true);
            $keys = array_column($map['stations'] ?? [], 'key');
            if (! is_array($map) || ! in_array('medeu', $keys, true) || ! in_array('shymbulak', $keys, true)) {
                continue;
            }
            $have = array_column($map['pins'] ?? [], 'id');
            foreach (LocationMap::dropoffPins() as $pin) {
                if (! in_array($pin['id'], $have, true)) {
                    $map['pins'][] = $pin;
                }
            }
            DB::table('locations')->where('id', $loc->id)->update(['map' => json_encode($map, JSON_UNESCAPED_UNICODE)]);
        }
    }

    public function down(): void
    {
        Schema::table('transports', fn (Blueprint $table) => $table->dropColumn(['price', 'orderable', 'map_pins']));
    }
};
