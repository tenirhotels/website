<?php

use App\Models\RoomType;
use Illuminate\Database\Migrations\Migration;

/** Параметры номера (площадь, гости, кровать, вид) становятся элементами единого списка; выбор «highlights» переходит в отметку pin. */
return new class extends Migration
{
    public function up(): void
    {
        foreach (RoomType::query()->get() as $room) {
            $features = is_array($room->features) ? $room->features : [];
            if (collect($features)->contains(fn ($f) => isset(RoomType::FACTS[$f['source'] ?? '']))) {
                continue;
            }
            $chosen = $room->highlights ?? ['size', 'guests', 'bed'];
            $facts = [];
            foreach (array_keys(RoomType::FACTS) as $k) {
                $facts[] = ['source' => $k, 'icon' => $k, 'pin' => in_array($k, $chosen, true), 'text' => []];
            }
            $rest = $features === [] ? $room->featuresFromLegacy() : array_map(fn ($f) => ['source' => 'custom'] + $f, $features);
            $all = array_merge($facts, $rest);
            $pins = 0;
            foreach ($all as &$f) {
                if (! empty($f['pin']) && ++$pins > RoomType::MAX_TILES) {
                    $f['pin'] = false;
                }
            }
            unset($f);
            $room->features = $all;
            $room->save();
        }
    }

    public function down(): void
    {
    }
};
