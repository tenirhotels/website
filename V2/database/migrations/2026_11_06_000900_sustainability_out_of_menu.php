<?php

use App\Models\Page;
use Illuminate\Database\Migrations\Migration;

/** Sustainability уходит из главного меню: ссылка — со страницы философии и в колонке «Бренд» подвала. Название переведено. */
return new class extends Migration
{
    public function up(): void
    {
        $sus = Page::where('slug', 'sustainability')->first();
        if ($sus) {
            $sus->placement = 'brand';
            $title = (array) $sus->title;
            if (($title['ru'] ?? null) === 'Sustainability') {
                $title['ru'] = 'Устойчивое развитие';
                $sus->title = $title;
            }
            $sus->save();
        }

        // Блок-ссылка на Sustainability внутри страницы «Наша философия» (перед финальным призывом).
        $about = Page::where('slug', 'about')->first();
        if (! $about) {
            return;
        }
        $content = (array) $about->content;
        $blocks = [
            'ru' => ['Бережно к горам', 'Как мы заботимся о природе: от материалов здания до рефиллов в ванных комнатах.', 'Устойчивое развитие'],
            'en' => ['Caring for the mountains', 'How we look after nature — from the building materials to refillable dispensers in the bathrooms.', 'Sustainability'],
        ];
        foreach ($blocks as $locale => [$heading, $text, $label]) {
            $list = array_values((array) ($content[$locale] ?? []));
            if ($list === [] || collect($list)->contains(fn ($b) => ($b['data']['button_url'] ?? null) === 'page:sustainability')) {
                continue;
            }
            $block = ['type' => 'cta', 'data' => ['heading' => $heading, 'text' => $text, 'button_label' => $label, 'button_url' => 'page:sustainability']];
            $last = count($list) - 1;
            if (($list[$last]['type'] ?? null) === 'cta') {
                array_splice($list, $last, 0, [$block]);
            } else {
                $list[] = $block;
            }
            $content[$locale] = $list;
        }
        $about->content = $content;
        $about->save();
    }

    public function down(): void
    {
        //
    }
};
