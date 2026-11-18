<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Своё фото для страниц «Как добраться» и «Перед приездом» — задаётся в локации. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('locations', function (Blueprint $t) {
            foreach (['getting_image', 'arrival_image'] as $col) {
                if (! Schema::hasColumn('locations', $col)) {
                    $t->string($col)->nullable();
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('locations', function (Blueprint $t) {
            foreach (['getting_image', 'arrival_image'] as $col) {
                if (Schema::hasColumn('locations', $col)) {
                    $t->dropColumn($col);
                }
            }
        });
    }
};
