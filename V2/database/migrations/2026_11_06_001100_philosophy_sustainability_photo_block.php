<?php

use App\Models\Page;
use Database\Seeders\Concerns\PhilosophyContent;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Storage;

/** Страница философии: блок про Sustainability — с фото и ссылкой вместо кнопки на фоне; «лиственница» по-казахски — балқарағай. */
return new class extends Migration
{
    public function up(): void
    {
        $image = null;
        $source = database_path('seeders/media/exp-4.webp');
        if (is_file($source)) {
            $image = 'seed/exp-4.webp';
            if (! Storage::disk('public')->exists($image)) {
                Storage::disk('public')->put($image, file_get_contents($source));
            }
        }

        $about = Page::where('slug', 'about')->first();
        if ($about) {
            $content = (array) $about->content;
            foreach (['ru', 'en', 'kk'] as $locale) {
                $list = array_values((array) ($content[$locale] ?? []));
                foreach ($list as $i => $block) {
                    if (($block['type'] ?? null) === 'cta' && ($block['data']['button_url'] ?? null) === 'page:sustainability') {
                        $list[$i] = PhilosophyContent::sustainability($locale, $image);
                    }
                }
                if ($list !== []) {
                    $content[$locale] = $list;
                }
            }
            $about->content = $content;
            $about->save();
        }

        $sus = Page::where('slug', 'sustainability')->first();
        if ($sus) {
            $content = json_decode(str_replace('Қарағай тақтайшадан', 'Балқарағай тақтайшадан', json_encode($sus->content, JSON_UNESCAPED_UNICODE)), true);
            $sus->content = $content;
            $sus->save();
        }
    }

    public function down(): void
    {
        //
    }
};
