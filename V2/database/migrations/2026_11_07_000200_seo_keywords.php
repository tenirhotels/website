<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Ключевые слова (по языкам) у каждой записи с SEO + набор по умолчанию для сайта. */
return new class extends Migration
{
    private array $tables = ['hotels', 'room_types', 'experiences', 'offers', 'events', 'pages', 'stories', 'venues'];

    public function up(): void
    {
        foreach ($this->tables as $t) {
            if (Schema::hasTable($t) && ! Schema::hasColumn($t, 'seo_keywords')) {
                Schema::table($t, fn (Blueprint $b) => $b->json('seo_keywords')->nullable());
            }
        }

        $seo = Setting::get('seo', []) ?: [];
        if (blank($seo['default_keywords'] ?? null)) {
            $seo['default_keywords'] = [
                'kk' => 'Шымбұлақ қонақүйі, тау қонақүйі, эко-қонақүй, Алматы, Tenir Hotels, ski-in ski-out, Іле Алатауы',
                'ru' => 'отель Шымбулак, горный отель, эко-отель, Алматы, Tenir Hotels, ski-in ski-out, Заилийский Алатау, отель в горах Казахстана',
                'en' => 'Shymbulak hotel, mountain hotel, eco hotel, Almaty, Tenir Hotels, ski-in ski-out, Trans-Ili Alatau, hotel in the mountains of Kazakhstan',
            ];
            Setting::put('seo', $seo);
        }
    }

    public function down(): void {}
};
