<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Ресторан: время перерыва и время подачи завтрака для постояльцев (переводимые, как «Часы работы»). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('venues', function (Blueprint $t) {
            foreach (['break_hours', 'breakfast_hours'] as $col) {
                if (! Schema::hasColumn('venues', $col)) {
                    $t->json($col)->nullable();
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('venues', function (Blueprint $t) {
            foreach (['break_hours', 'breakfast_hours'] as $col) {
                if (Schema::hasColumn('venues', $col)) {
                    $t->dropColumn($col);
                }
            }
        });
    }
};
