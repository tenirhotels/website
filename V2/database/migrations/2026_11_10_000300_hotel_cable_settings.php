<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Канатная дорога — часть локации отеля, а не настройка сайта: у отелей в других местах её нет.
 * Колонка hotels.cable (json): enabled, hours_*, hours_note, guest_from/to, photo_*.
 * Существующие настройки сайта (группа cable) переносим в отель, который стоит на курорте (config tenir.recognition_hotel).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->json('cable')->nullable()->after('intro_image');
        });

        $old = (array) Setting::get('cable', []);
        $slug = config('tenir.recognition_hotel', 'shymbulak');
        $id = DB::table('hotels')->where('slug', $slug)->value('id');
        if ($id) {
            DB::table('hotels')->where('id', $id)->update(['cable' => json_encode(['enabled' => true] + array_filter($old, fn ($v) => $v !== null && $v !== ''))]);
        }
    }

    public function down(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->dropColumn('cable');
        });
    }
};
