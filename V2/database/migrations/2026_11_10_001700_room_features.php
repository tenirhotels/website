<?php

use App\Models\RoomType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Удобства номера становятся управляемым списком: {icon, pin, text{kk,ru,en,zh}}.
 * Старые списки по языкам переносятся по порядку (как они и были выровнены); ничего не удаляется.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('room_types', 'features')) {
            Schema::table('room_types', fn (Blueprint $t) => $t->json('features')->nullable());
        }

        foreach (RoomType::query()->get() as $room) {
            if (! empty($room->features)) {
                continue;
            }
            $items = $room->featuresFromLegacy();
            if ($items === []) {
                continue;
            }
            $room->features = $items;
            if ($room->highlights === null) {
                $room->highlights = ['size', 'guests', 'bed'];
            }
            $room->save();
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('room_types', 'features')) {
            Schema::table('room_types', fn (Blueprint $t) => $t->dropColumn('features'));
        }
    }
};
