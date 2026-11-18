<?php

use App\Models\Page;
use Database\Seeders\Concerns\SustainabilityContent;
use Illuminate\Database\Migrations\Migration;

/** Sustainability: текст прозой (рефиллы, мыло в бумаге, сбор воды, сезонная кухня), без «Leave the mountains…». */
return new class extends Migration
{
    public function up(): void
    {
        $page = Page::where('slug', 'sustainability')->first();
        if (! $page) {
            return;
        }
        // Если текст уже правили вручную в админке — не затираем.
        $ru = (array) (((array) $page->content)['ru'] ?? []);
        if (($ru[0]['data']['heading'] ?? null) !== null && ! in_array($ru[0]['data']['heading'], [SustainabilityContent::OLD_RU_HEADING, 'В горах мы в гостях'], true)) {
            return;
        }
        $page->content = SustainabilityContent::content();
        $page->summary = SustainabilityContent::summary();
        $page->save();
    }

    public function down(): void
    {
        //
    }
};
