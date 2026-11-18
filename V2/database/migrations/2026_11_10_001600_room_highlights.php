<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('room_types', 'highlights')) {
            Schema::table('room_types', fn (Blueprint $t) => $t->json('highlights')->nullable());
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('room_types', 'highlights')) {
            Schema::table('room_types', fn (Blueprint $t) => $t->dropColumn('highlights'));
        }
    }
};
