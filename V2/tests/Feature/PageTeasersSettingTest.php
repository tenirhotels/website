<?php

namespace Tests\Feature;

use App\Models\Hotel;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageTeasersSettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_teasers_come_from_settings_with_lang_fallback(): void
    {
        $this->seed();
        $hotel = Hotel::query()->firstOrFail();
        $this->get($hotel->sectionUrl('getting-here'))->assertOk()->assertSee(__('site.hotel.before_tease'));

        $hotel->location->update(['arrival_info' => ['ru' => '<p>Важно знать</p>']]);
        Setting::put('pages', ['before_tease' => ['ru' => 'Мой текст плашки'], 'getting_tease' => ['ru' => 'Другой текст']]);
        app()->setLocale('ru');
        $this->get($hotel->sectionUrl('getting-here'))->assertSee('Мой текст плашки');
        $this->get($hotel->sectionUrl('before-you-arrive'))->assertSee('Другой текст')->assertSee('Мой текст плашки');
    }
}
