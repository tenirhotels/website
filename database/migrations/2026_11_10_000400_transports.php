<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Транспорт локации — отдельная сущность отеля, как номера и рестораны (канатная дорога, трансфер, такси…).
 * Данные из hotels.cable (прошлый шаг) переносим в записи и убираем колонку.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained()->cascadeOnDelete();
            $table->string('kind')->default('other'); // cable_line | guest_ride | shuttle | taxi | other
            $table->string('code')->nullable();       // для линий канатной дороги: M-1, K-1, K-2
            $table->json('name')->nullable();
            $table->json('summary')->nullable();
            $table->string('hours')->nullable();      // «09:00–18:00»
            $table->json('note')->nullable();         // сезон, дни, оговорки
            $table->string('image')->nullable();
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        if (! Schema::hasColumn('hotels', 'cable')) {
            return;
        }
        foreach (DB::table('hotels')->whereNotNull('cable')->get(['id', 'cable']) as $h) {
            $c = json_decode($h->cable, true) ?: [];
            if (empty($c['enabled'])) {
                continue;
            }
            $note = ! empty($c['hours_note']) ? ['ru' => $c['hours_note']] : null;
            $rows = [
                ['M-1', 'hours_medeu', 'photo_medeu', 1, $note],
                ['K-1', 'hours_combi1', 'photo_shymbulak', 2, null],
                ['K-2', 'hours_combi2', 'photo_combi1', 3, null],
            ];
            foreach ($rows as [$code, $hk, $pk, $sort, $n]) {
                DB::table('transports')->insert([
                    'hotel_id' => $h->id, 'kind' => 'cable_line', 'code' => $code, 'hours' => $c[$hk] ?? null,
                    'image' => $c[$pk] ?? null, 'note' => $n ? json_encode($n) : null, 'sort' => $sort,
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }
            if (! empty($c['guest_from']) || ! empty($c['guest_to'])) {
                DB::table('transports')->insert([
                    'hotel_id' => $h->id, 'kind' => 'guest_ride', 'sort' => 4,
                    'hours' => ($c['guest_from'] ?? '18:00').'–'.($c['guest_to'] ?? '18:30'),
                    'name' => json_encode(['ru' => 'Особый подъём для гостей отеля']),
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }
        }
        Schema::table('hotels', fn (Blueprint $t) => $t->dropColumn('cable'));
    }

    public function down(): void
    {
        Schema::dropIfExists('transports');
    }
};
