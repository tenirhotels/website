<?php

use App\Models\Location;
use App\Support\ResortMap;
use Illuminate\Database\Migrations\Migration;

/** Координаты меток карты Шымбулака: только там, где они ещё не заданы (правки редактора не трогаем). */
return new class extends Migration
{
    public function up(): void
    {
        foreach (Location::query()->get() as $location) {
            $map = $location->map;
            if (! is_array($map) || empty($map['pins']) || ($location->slug ?? null) !== 'shymbulak') {
                continue;
            }
            $changed = false;
            foreach ($map['pins'] as $i => $pin) {
                $c = ResortMap::COORDS[$pin['id'] ?? ''] ?? null;
                if ($c && blank($pin['lat'] ?? null) && blank($pin['lng'] ?? null)) {
                    $map['pins'][$i]['lat'] = $c[0];
                    $map['pins'][$i]['lng'] = $c[1];
                    $changed = true;
                }
            }
            if ($changed) {
                $location->map = $map;
                $location->save();
            }
        }
    }

    public function down(): void {}
};
