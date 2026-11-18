<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Отдельное фото для блока «О отеле» на главной (раньше бралось из галереи — первое фото, что было неочевидно в админке). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->string('intro_image')->nullable()->after('hero_image_summer');
        });
    }

    public function down(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->dropColumn('intro_image');
        });
    }
};
