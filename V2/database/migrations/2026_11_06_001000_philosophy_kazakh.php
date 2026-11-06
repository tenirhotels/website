<?php

use App\Models\Page;
use Database\Seeders\Concerns\PhilosophyContent;
use Illuminate\Database\Migrations\Migration;

/** Казахская версия страницы «Наша философия» (раньше показывался русский текст). */
return new class extends Migration
{
    public function up(): void
    {
        $about = Page::where('slug', 'about')->first();
        if (! $about) {
            return;
        }
        $content = (array) $about->content;
        if (! empty($content['kk'])) {
            return; // уже заполнено вручную
        }
        $image = collect((array) ($content['en'] ?? []))->firstWhere('type', 'image_text')['data']['image'] ?? null;
        $content['kk'] = PhilosophyContent::kk($image);
        $about->content = $content;
        $about->summary = array_merge((array) $about->summary, ['kk' => 'Табиғатты және онымен байланысты қайта сезіну.']);
        $about->save();
    }

    public function down(): void
    {
        //
    }
};
