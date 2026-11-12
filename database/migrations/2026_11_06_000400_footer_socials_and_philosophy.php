<?php

use App\Models\Page;
use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;

/** Соцсети (Instagram, YouTube, TikTok, SoundCloud) и страница «Наша философия» (бывшая «О бренде»). */
return new class extends Migration
{
    public function up(): void
    {
        $social = Setting::get('social', []) ?: [];
        // Facebook был только заготовкой сидера — аккаунта нет.
        if (($social['facebook'] ?? null) === 'https://facebook.com/tenirhotels') {
            unset($social['facebook']);
        }
        Setting::put('social', array_merge($social, [
            'instagram' => 'https://instagram.com/tenir.ecohotels',
            'youtube' => 'https://youtube.com/@tenirhotels',
            'tiktok' => 'https://www.tiktok.com/@tenirhotels',
            'soundcloud' => 'https://soundcloud.com/tenirhotels',
        ]));

        // Название страницы меняем, только если его не правили вручную.
        $about = Page::where('slug', 'about')->first();
        if ($about) {
            $title = (array) $about->title;
            if (($title['ru'] ?? null) === 'О бренде') {
                $about->title = ['ru' => 'Наша философия', 'en' => 'Our philosophy', 'kk' => 'Біздің философия', 'zh' => '我们的理念'];
                $about->save();
            }
        }
    }

    public function down(): void
    {
        //
    }
};
