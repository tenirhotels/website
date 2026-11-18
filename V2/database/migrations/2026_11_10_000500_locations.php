<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Локация — отдельная сущность: сначала создаётся локация со всеми настройками места (адрес, координаты, высота,
 * «как добраться», транспорт), затем к ней привязывается отель. Данные из отелей переносим в локации.
 */
return new class extends Migration
{
    private const MOVED = ['region', 'city', 'address', 'getting_here', 'altitude_m', 'country_code', 'latitude', 'longitude'];

    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->json('name')->nullable();
            $table->json('region')->nullable();
            $table->json('city')->nullable();
            $table->json('address')->nullable();
            $table->json('getting_here')->nullable();
            $table->unsignedInteger('altitude_m')->nullable();
            $table->string('country_code', 2)->default('KZ');
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::table('hotels', function (Blueprint $table) {
            $table->foreignId('location_id')->nullable()->after('id')->constrained('locations')->nullOnDelete();
        });

        $used = [];
        foreach (DB::table('hotels')->orderBy('id')->get() as $h) {
            // Один отель — одна локация (отели с совпадающими координатами делят локацию).
            $key = $h->latitude && $h->longitude ? round($h->latitude, 4).','.round($h->longitude, 4) : 'hotel-'.$h->id;
            if (! isset($used[$key])) {
                $slug = $h->slug === 'shymbulak' ? 'shymbulak' : $h->slug;
                $name = $h->slug === 'shymbulak'
                    ? ['ru' => 'Шымбулак', 'en' => 'Shymbulak', 'kk' => 'Шымбұлақ', 'zh' => 'Shymbulak']
                    : (json_decode($h->name ?? 'null', true) ?: ['ru' => $h->slug]);
                $used[$key] = DB::table('locations')->insertGetId([
                    'slug' => $slug, 'name' => json_encode($name, JSON_UNESCAPED_UNICODE),
                    'region' => $h->region, 'city' => $h->city, 'address' => $h->address, 'getting_here' => $h->getting_here,
                    'altitude_m' => $h->altitude_m, 'country_code' => $h->country_code ?: 'KZ',
                    'latitude' => $h->latitude, 'longitude' => $h->longitude,
                    'sort' => 0, 'created_at' => now(), 'updated_at' => now(),
                ]);
            }
            DB::table('hotels')->where('id', $h->id)->update(['location_id' => $used[$key]]);
        }

        // Транспорт принадлежит локации, а не отелю.
        Schema::table('transports', function (Blueprint $table) {
            $table->foreignId('location_id')->nullable()->after('id')->constrained('locations')->cascadeOnDelete();
        });
        foreach (DB::table('transports')->get(['id', 'hotel_id']) as $t) {
            DB::table('transports')->where('id', $t->id)->update([
                'location_id' => DB::table('hotels')->where('id', $t->hotel_id)->value('location_id'),
            ]);
        }
        Schema::table('transports', fn (Blueprint $table) => $table->dropConstrainedForeignId('hotel_id'));

        Schema::table('hotels', fn (Blueprint $table) => $table->dropColumn(self::MOVED));
    }

    public function down(): void
    {
        // Откат не поддерживается: данные уже живут в локациях.
    }
};
