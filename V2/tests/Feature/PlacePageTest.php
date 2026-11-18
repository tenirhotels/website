<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Support\Weather;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PlacePageTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    private function loc(): Location
    {
        return Location::where('slug', 'shymbulak')->firstOrFail();
    }

    private function enable(array $extra = []): Location
    {
        $loc = $this->loc();
        $loc->place = array_replace_recursive((array) $loc->place, ['enabled' => true], $extra);
        $loc->save();

        return $loc;
    }

    public function test_page_is_hidden_until_enabled_and_has_no_footer_link(): void
    {
        $this->assertFalse($this->loc()->hasPlacePage());
        $this->get('/ru/place/shymbulak')->assertNotFound();
        $this->get('/ru')->assertOk()->assertDontSee('/ru/place/shymbulak', false);
        $this->get('/sitemap.xml')->assertDontSee('/place/shymbulak', false);
    }

    public function test_enabled_page_renders_in_every_language_with_links(): void
    {
        $this->enable();
        foreach (['kk', 'ru', 'en', 'zh'] as $lang) {
            $this->get("/$lang/place/shymbulak")->assertOk()->assertSee('data-place', false);
        }
        $this->get('/ru/place/shymbulak')->assertSee('Заилийский Алатау')->assertSee('3 200');
        $this->get('/ru')->assertSee('/ru/place/shymbulak', false);
        $this->get('/ru/getting-here')->assertSee('/ru/place/shymbulak', false);
        $this->get('/sitemap.xml')->assertSee('/ru/place/shymbulak', false);
        $this->assertStringContainsString('TouristDestination', $this->get('/en/place/shymbulak')->getContent());
        $this->assertMatchesRegularExpression('~<title>Шымбулак — ~u', $this->get('/ru/place/shymbulak')->getContent());
    }

    public function test_cameras_trails_and_weather_blocks(): void
    {
        $this->enable([
            'cameras' => [['title' => ['ru' => 'Вид на хребет'], 'image_url' => 'https://cam.example.com/a.jpg', 'refresh' => 90]],
            'trails' => [
                ['name' => ['ru' => 'Трасса 5'], 'level' => 'red', 'length_m' => 1200, 'status' => 'closed'],
                ['name' => ['ru' => 'Трасса 1'], 'level' => 'green', 'length_m' => 800, 'status' => 'open'],
            ],
        ]);

        $html = $this->get('/ru/place/shymbulak')->assertOk()->getContent();
        $this->assertStringContainsString('data-src="https://cam.example.com/a.jpg"', $html);
        $this->assertStringContainsString('Вид на хребет', $html);
        $this->assertStringContainsString('Трасса 5', $html);
        $this->assertStringContainsString('Закрыта', $html);
        $this->assertLessThan(strpos($html, 'Трасса 5'), strpos($html, 'Трасса 1'), 'зелёные идут раньше красных');
        $this->assertStringContainsString('/api/weather/shymbulak/3200', $html);
    }

    public function test_weather_endpoint_caches_and_survives_provider_outage(): void
    {
        $this->enable();
        Cache::flush();
        Http::fake(['api.open-meteo.com/*' => Http::response([
            'current' => ['temperature_2m' => -6.4, 'apparent_temperature' => -11.2, 'weather_code' => 71, 'wind_speed_10m' => 5.2, 'wind_gusts_10m' => 9.1, 'relative_humidity_2m' => 70, 'is_day' => 1],
            'daily' => ['time' => ['2026-11-18', '2026-11-19'], 'weather_code' => [71, 3], 'temperature_2m_max' => [-4, -2], 'temperature_2m_min' => [-9, -8], 'snowfall_sum' => [3.2, 0], 'precipitation_sum' => [2, 0], 'wind_speed_10m_max' => [8, 6]],
        ])]);

        $json = $this->getJson('/api/weather/shymbulak/3200')->assertOk()->json();
        $this->assertSame(-6, $json['now']['temp']);
        $this->assertCount(2, $json['days']);
        $this->getJson('/api/weather/shymbulak/3200')->assertOk();
        Http::assertSentCount(1); // второй ответ — из кэша

        // сервис упал, свежий кэш истёк — отдаётся последний удачный ответ
        Cache::forget(sprintf('weather:%.3f:%.3f:%d', $this->loc()->latitude, $this->loc()->longitude, 3200));
        Http::fake(['api.open-meteo.com/*' => Http::response('boom', 500)]);
        $this->assertSame(-6, Weather::for($this->loc()->latitude, $this->loc()->longitude, 3200)['now']['temp']);

        // чужая высота и выключенная страница — 404
        $this->getJson('/api/weather/shymbulak/1234')->assertNotFound();
    }

    public function test_admin_location_form_has_place_tab(): void
    {
        $this->actingAs(\App\Models\User::factory()->create());
        $this->get('/admin/locations/'.$this->loc()->id.'/edit')->assertOk()->assertSee('Страница локации');
    }
}
