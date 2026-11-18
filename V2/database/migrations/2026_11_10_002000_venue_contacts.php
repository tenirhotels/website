<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('venues', function (Blueprint $t) {
            foreach (['phone', 'phone_ext', 'email'] as $col) {
                if (! Schema::hasColumn('venues', $col)) {
                    $t->string($col)->nullable();
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('venues', function (Blueprint $t) {
            foreach (['phone', 'phone_ext', 'email'] as $col) {
                if (Schema::hasColumn('venues', $col)) {
                    $t->dropColumn($col);
                }
            }
        });
    }
};
