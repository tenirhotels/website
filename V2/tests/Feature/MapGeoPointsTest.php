<?php

namespace Tests\Feature;

use App\Models\Hotel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MapGeoPointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_pins_with_coordinates_become_control_points_without_duplicates(): void
    {
        $this->seed();
        $map = Hotel::where('slug', 'shymbulak')->firstOrFail()->resortMap();
        $points = $map->geoPoints();

        $this->assertGreaterThanOrEqual(10, count($points));
        // старая точка отеля (в 40 м от метки) не дублируется
        $this->assertEmpty(collect($points)->filter(fn ($p) => abs($p[0] - 43.113991) < 1e-6));
        $this->assertNotEmpty(collect($points)->filter(fn ($p) => abs($p[0] - 43.113893) < 1e-6));
        $this->assertNotEmpty($map->geoDeviations());
    }

    public function test_map_page_renders_all_points(): void
    {
        $this->seed();
        $this->get('/ru/map')->assertOk()->assertSee('data-map-geo', false)
            ->assertSee('data-acc', false)->assertSee('here_acc', false)->assertSee('approx', false);
    }
}
