<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Модель контента сайта сети Tenir Hotels.
 * Переводимые поля — JSON {"ru": ..., "kk": ..., "en": ..., "zh": ...}.
 */
return new class extends Migration
{
    private function seo(Blueprint $table): void
    {
        $table->json('seo_title')->nullable();
        $table->json('seo_description')->nullable();
        $table->string('og_image')->nullable();
        $table->boolean('is_published')->default(true)->index();
        $table->unsignedInteger('sort')->default(0);
    }

    public function up(): void
    {
        // Отели (направления): /{locale}/destinations/{slug}
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->json('name');
            $table->json('tagline')->nullable();          // «Ближе к небу»
            $table->json('summary')->nullable();          // короткое описание для карточек и SEO
            $table->json('description')->nullable();      // основной текст (HTML)
            $table->string('status')->default('open');    // open | coming_soon
            $table->json('opening_label')->nullable();    // «Открытие в 2028»
            $table->json('region')->nullable();           // «Заилийский Алатау»
            $table->json('city')->nullable();             // «Алматы»
            $table->string('country_code', 2)->default('KZ');
            $table->json('address')->nullable();
            $table->unsignedInteger('altitude_m')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('email')->nullable();
            $table->string('check_in_time', 5)->nullable();
            $table->string('check_out_time', 5)->nullable();
            $table->string('exely_context')->nullable();  // если у отеля свой контекст TravelLine
            $table->string('hero_image')->nullable();
            $table->string('hero_video')->nullable();
            $table->json('gallery')->nullable();          // пути к фото
            $table->json('highlights')->nullable();       // [{value, label:{ru,en}}] — «3 200 м · над уровнем моря»
            $table->json('getting_here')->nullable();     // как добраться (HTML)
            $table->json('reviews_widget')->nullable();   // код виджета отзывов Exely, если используется
            $table->boolean('is_featured')->default(true);
            $this->seo($table);
            $table->timestamps();
        });

        // Типы номеров: /{locale}/destinations/{hotel}/rooms/{slug}
        Schema::create('room_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained()->cascadeOnDelete();
            $table->string('slug');
            $table->json('name');
            $table->json('summary')->nullable();
            $table->json('description')->nullable();
            $table->unsignedSmallInteger('size_m2')->nullable();
            $table->unsignedTinyInteger('max_guests')->nullable();
            $table->json('bed')->nullable();
            $table->json('view')->nullable();
            $table->json('amenities')->nullable();        // {"ru": ["Финская сауна", ...], "en": [...]}
            $table->string('image')->nullable();
            $table->json('gallery')->nullable();
            $table->string('exely_room_type_id')->nullable();
            $table->unsignedInteger('price_from')->nullable();
            $this->seo($table);
            $table->timestamps();
            $table->unique(['hotel_id', 'slug']);
        });

        // Спецпредложения: /{locale}/offers/{slug} (+ на странице отеля)
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->nullable()->constrained()->nullOnDelete(); // null — на всю сеть
            $table->string('slug')->unique();
            $table->json('title');
            $table->json('summary')->nullable();
            $table->json('description')->nullable();
            $table->json('terms')->nullable();
            $table->string('image')->nullable();
            $table->string('exely_special_offer_id')->nullable();
            $table->string('promo_code')->nullable();
            $table->unsignedTinyInteger('min_nights')->nullable();
            $table->date('valid_from')->nullable();
            $table->date('valid_to')->nullable();
            $table->boolean('is_featured')->default(false);
            $this->seo($table);
            $table->timestamps();
        });

        // Впечатления: /{locale}/experiences/{slug} (+ на странице отеля)
        Schema::create('experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->nullable()->constrained()->nullOnDelete();
            $table->string('slug')->unique();
            $table->string('category')->index();          // ключ из config('tenir.experience_categories')
            $table->json('seasons')->nullable();          // ["winter","summer"]
            $table->json('title');
            $table->json('summary')->nullable();
            $table->json('description')->nullable();
            $table->json('duration')->nullable();         // «60 минут»
            $table->unsignedInteger('price_from')->nullable();
            $table->string('image')->nullable();
            $table->json('gallery')->nullable();
            $table->string('exely_service_id')->nullable(); // доп. услуга в Exely, если продаётся при бронировании
            $table->boolean('is_featured')->default(false);
            $this->seo($table);
            $table->timestamps();
        });

        // Рестораны и бары: /{locale}/destinations/{hotel}/dining
        Schema::create('venues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained()->cascadeOnDelete();
            $table->string('slug');
            $table->string('type')->default('restaurant'); // restaurant | bar | terrace
            $table->json('name');
            $table->json('summary')->nullable();
            $table->json('description')->nullable();
            $table->json('hours')->nullable();             // «08:30–23:00»
            $table->string('menu_file')->nullable();
            $table->string('image')->nullable();
            $table->json('gallery')->nullable();
            $this->seo($table);
            $table->timestamps();
            $table->unique(['hotel_id', 'slug']);
        });

        // Страницы бренда из блоков: /{locale}/{slug} — about, sustainability, legal...
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->json('title');
            $table->json('summary')->nullable();
            $table->string('hero_image')->nullable();
            $table->json('content')->nullable();           // {"ru": [блоки], "en": [блоки]}
            $table->string('placement')->default('main');  // main | footer | hidden
            $this->seo($table);
            $table->timestamps();
        });

        // Истории (журнал / новости): /{locale}/stories/{slug}
        Schema::create('stories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->nullable()->constrained()->nullOnDelete();
            $table->string('slug')->unique();
            $table->json('title');
            $table->json('summary')->nullable();
            $table->json('body')->nullable();
            $table->string('image')->nullable();
            $table->timestamp('published_at')->nullable()->index();
            $this->seo($table);
            $table->timestamps();
        });

        // Частые вопросы (отель или вся сеть)
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->nullable()->constrained()->cascadeOnDelete();
            $table->json('question');
            $table->json('answer');
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        // 301-редиректы со старых адресов (V1 и конструктор Exely)
        Schema::create('redirects', function (Blueprint $table) {
            $table->id();
            $table->string('from_path')->unique();          // без домена, без query: /en-gb/rooms
            $table->string('to_path');
            $table->unsignedSmallInteger('status_code')->default(301);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('hits')->default(0);
            $table->timestamp('last_hit_at')->nullable();
            $table->timestamps();
        });

        // Настройки сайта (контакты, соцсети, аналитика, SEO по умолчанию)
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->json('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['settings', 'redirects', 'faqs', 'stories', 'pages', 'venues', 'experiences', 'offers', 'room_types', 'hotels'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
