<?php

namespace Tests\Feature;

use App\Filament\Pages\MapCalibration;
use App\Models\Hotel;
use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MapCalibrationTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    private function location(): Location
    {
        return Hotel::where('slug', 'shymbulak')->firstOrFail()->location;
    }

    public function test_page_renders_with_map_config(): void
    {
        $this->actingAs(User::factory()->create());
        $this->get('/admin/map-calibration')->assertOk()->assertSee('data-geo', false)->assertSee('Привязка карты');

        $cfg = Livewire::test(MapCalibration::class)->instance()->config();
        $this->assertNotEmpty($cfg['image']);
        $this->assertGreaterThanOrEqual(10, count($cfg['pins']));
        $this->assertArrayHasKey('hotel', collect($cfg['pins'])->keyBy('id'));
    }

    public function test_save_stores_clean_points_and_ignored_pins(): void
    {
        $this->actingAs(User::factory()->create());
        Livewire::test(MapCalibration::class)->call('save', [
            ['lat' => '43.1291', 'lng' => 77.08288, 'x' => 600.4, 'y' => 99999],
            ['lat' => 'x', 'lng' => 1, 'x' => 1, 'y' => 1],          // мусор отбрасывается
        ], ['r1', 'unknown-pin']);

        $map = $this->location()->fresh()->map;
        $this->assertCount(1, $map['geo_points']);
        $this->assertSame(600, $map['geo_points'][0]['x']);
        $this->assertSame($this->location()->resortMap()->height(), $map['geo_points'][0]['y']); // зажато границей рисунка
        $this->assertSame(['r1'], $map['geo_ignore']);
    }

    public function test_ignored_pin_is_not_a_control_point_and_frees_its_neighbourhood(): void
    {
        $before = $this->location()->resortMap()->geoPoints();
        $loc = $this->location();
        $data = $loc->map;
        $data['geo_ignore'] = ['hotel'];
        $loc->map = $data;
        $loc->save();

        $after = $this->location()->fresh()->resortMap()->geoPoints();
        $this->assertCount(count($before) - 1, $after);
        $this->assertEmpty(collect($after)->filter(fn ($p) => abs($p[0] - 43.113893) < 1e-6));
    }
}
