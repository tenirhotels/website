<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Часы линий канатной дороги раньше подставлялись из кода, если в настройках было пусто.
 * Теперь они хранятся в «Транспорте» — заполняем пустые значения часами курорта по умолчанию
 * и добавляем «особый подъём для гостей», если его нет.
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach (['M-1' => '09:00–18:00', 'K-1' => '09:00–17:00', 'K-2' => '09:00–16:30'] as $code => $hours) {
            DB::table('transports')->where('kind', 'cable_line')->where('code', $code)
                ->where(fn ($q) => $q->whereNull('hours')->orWhere('hours', ''))
                ->update(['hours' => $hours]);
        }

        foreach (DB::table('transports')->where('kind', 'cable_line')->whereNotNull('location_id')->distinct()->pluck('location_id') as $id) {
            if (DB::table('transports')->where('location_id', $id)->where('kind', 'guest_ride')->doesntExist()) {
                DB::table('transports')->insert([
                    'location_id' => $id, 'kind' => 'guest_ride', 'hours' => '18:00–18:30', 'sort' => 4, 'is_published' => true,
                    'name' => json_encode(['ru' => 'Особый подъём для гостей отеля', 'en' => 'Special ride for hotel guests'], JSON_UNESCAPED_UNICODE),
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        // Данные остаются.
    }
};
