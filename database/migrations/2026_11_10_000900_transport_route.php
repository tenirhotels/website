<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Такси и трансфер: маршрут «откуда → куда». */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transports', function (Blueprint $table) {
            $table->json('route_from')->nullable()->after('summary');
            $table->json('route_to')->nullable()->after('route_from');
        });
    }

    public function down(): void
    {
        Schema::table('transports', fn (Blueprint $table) => $table->dropColumn(['route_from', 'route_to']));
    }
};
