<?php

use App\Models\Location;
use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Тексты плашек между «Как добраться» и «Перед приездом» живут в локации. Если успели задать в настройках сайта — переносим. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('locations', function (Blueprint $t) {
            foreach (['getting_tease', 'before_tease'] as $col) {
                if (! Schema::hasColumn('locations', $col)) {
                    $t->json($col)->nullable();
                }
            }
        });

        $old = Setting::query()->where('key', 'pages')->first();
        if ($old && is_array($old->value)) {
            foreach (Location::query()->get() as $location) {
                foreach (['getting_tease', 'before_tease'] as $col) {
                    if (empty($location->{$col}) && ! empty($old->value[$col])) {
                        $location->{$col} = array_filter((array) $old->value[$col]);
                    }
                }
                $location->save();
            }
            $old->delete();
        }
    }

    public function down(): void
    {
        Schema::table('locations', function (Blueprint $t) {
            foreach (['getting_tease', 'before_tease'] as $col) {
                if (Schema::hasColumn('locations', $col)) {
                    $t->dropColumn($col);
                }
            }
        });
    }
};
