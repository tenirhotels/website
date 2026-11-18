<?php

namespace Tests\Feature;

use App\Models\Hotel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocationTeasersTest extends TestCase
{
    use RefreshDatabase;

    public function test_teasers_come_from_location_with_lang_fallback(): void
    {
        $this->seed();
        $hotel = Hotel::query()->firstOrFail();
        $hotel->location->update([
            'arrival_info' => ['ru' => '<p>Важно знать</p>'],
            'before_tease' => ['ru' => 'Мой текст плашки'],
            'getting_tease' => ['ru' => 'Другой текст'],
        ]);
        app()->setLocale('ru');
        $hotel = Hotel::query()->firstOrFail();
        $this->get($hotel->sectionUrl('getting-here'))->assertSee('Мой текст плашки');
        $this->get($hotel->sectionUrl('before-you-arrive'))->assertSee('Другой текст')->assertSee('Мой текст плашки');
    }
}
