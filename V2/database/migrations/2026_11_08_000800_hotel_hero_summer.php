<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Летнее фото героя: главная показывает зимнее (hero_image) с ноября по апрель, летнее — с мая по октябрь. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->string('hero_image_summer')->nullable()->after('hero_image');
        });
    }

    public function down(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->dropColumn('hero_image_summer');
        });
    }
};
