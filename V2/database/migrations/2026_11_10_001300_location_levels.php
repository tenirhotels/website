<?php

use App\Models\Hotel;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** «От города до вершины»: уровни высоты локации (Алматы, Медеу, Шымбулак… отель) для страницы «Перед приездом». */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('locations', 'levels')) {
            Schema::table('locations', function (Blueprint $table) {
                $table->json('levels')->nullable()->after('arrival_info');
            });
        }

        $location = Hotel::where('slug', 'shymbulak')->first()?->location;
        if ($location && blank($location->levels)) {
            $location->levels = [
                ['name' => ['kk' => 'Алматы', 'ru' => 'Алматы', 'en' => 'Almaty', 'zh' => '阿拉木图'], 'altitude_m' => 900],
                ['name' => ['kk' => 'Медеу', 'ru' => 'Медеу', 'en' => 'Medeu', 'zh' => 'Medeu'], 'altitude_m' => 1700],
                ['name' => ['kk' => 'Шымбұлақ', 'ru' => 'Шымбулак', 'en' => 'Shymbulak', 'zh' => 'Shymbulak'], 'altitude_m' => 2200],
                ['name' => ['kk' => 'Аралық станция', 'ru' => 'Средняя станция', 'en' => 'Mid station', 'zh' => '中间站'], 'altitude_m' => 2800],
                ['name' => ['kk' => 'Tenir қонақүйі', 'ru' => 'Отель Tenir', 'en' => 'Tenir hotel', 'zh' => 'Tenir 酒店'], 'altitude_m' => 3200],
            ];
            $location->save();
        }
    }

    public function down(): void
    {
        Schema::table('locations', fn (Blueprint $table) => $table->dropColumn('levels'));
    }
};
