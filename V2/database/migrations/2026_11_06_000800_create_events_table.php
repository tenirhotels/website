<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Афиша: события курорта и отелей с датами (/{locale}/events). BBQ-вечеринка была разовой — снимаем с «Впечатлений». */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->nullable()->constrained()->nullOnDelete(); // пусто — событие всего курорта
            $table->string('slug')->unique();
            $table->date('starts_on')->index();
            $table->date('ends_on')->nullable();
            $table->string('time_text')->nullable();     // «12:30–16:30»
            $table->json('title');
            $table->json('summary')->nullable();
            $table->json('description')->nullable();
            $table->json('place')->nullable();           // «Терраса Tenir, 3 200 м»
            $table->string('image')->nullable();
            $table->string('cta')->default('booking');   // booking | sales | contact | none
            $table->boolean('is_featured')->default(false);
            $table->json('seo_title')->nullable();
            $table->json('seo_description')->nullable();
            $table->string('og_image')->nullable();
            $table->boolean('is_published')->default(true)->index();
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        DB::table('experiences')->where('slug', 'bbq-party')->update(['is_published' => false]);
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
