<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Страница локации /place/{slug}: вступление, «О месте», фото, а также факты, камеры, трассы и погода (json). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->json('lead')->nullable();
            $table->json('about')->nullable();
            $table->string('place_image')->nullable();
            $table->json('place')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->dropColumn(['lead', 'about', 'place_image', 'place']);
        });
    }
};
