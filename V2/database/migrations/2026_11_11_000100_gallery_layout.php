<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Вид показа галереи (мозаика / слайдер / сетка / колонки) — выбирается у номера, ресторана, активности и отеля. */
return new class extends Migration
{
    private const TABLES = ['room_types', 'venues', 'experiences', 'hotels'];

    public function up(): void
    {
        foreach (self::TABLES as $table) {
            if (! Schema::hasColumn($table, 'gallery_layout')) {
                Schema::table($table, fn (Blueprint $t) => $t->string('gallery_layout', 20)->nullable());
            }
        }
    }

    public function down(): void
    {
        foreach (self::TABLES as $table) {
            if (Schema::hasColumn($table, 'gallery_layout')) {
                Schema::table($table, fn (Blueprint $t) => $t->dropColumn('gallery_layout'));
            }
        }
    }
};
