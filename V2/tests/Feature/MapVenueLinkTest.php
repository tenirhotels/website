<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\Venue;
use App\Support\LocationMap;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MapVenueLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_restaurant_pin_takes_name_hours_and_link_from_venue(): void
    {
        $this->seed();
        $venue = Venue::query()->with('hotel')->firstOrFail();
        $location = $venue->hotel->location;
        $map = $location->map;
        foreach ($map['pins'] as &$pin) {
            if ($pin['kind'] === 'restaurant') {
                $pin['venue_id'] = $venue->id;
                $pin['label'] = ['ru' => 'Старое название'];
                break;
            }
        }
        unset($pin);
        $location->map = $map;
        $location->save();

        $pins = LocationMap::for($location->fresh())->pins()->where('kind', 'restaurant');
        $linked = $pins->firstWhere('venue_id', $venue->id);
        $this->assertSame($venue->t('name'), $linked['label']);
        $this->assertStringEndsWith('#venue-'.$venue->id, $linked['venue_url']);

        $this->get($venue->hotel->sectionUrl('dining'))->assertOk()->assertSee('id="venue-'.$venue->id.'"', false);
    }

    public function test_unlinked_pin_has_no_link(): void
    {
        $this->seed();
        $location = Location::query()->whereNotNull('map')->firstOrFail();
        $map = $location->map;
        foreach ($map['pins'] as &$pin) {
            unset($pin['venue_id']);
        }
        unset($pin);
        $location->map = $map;
        $location->save();

        foreach (LocationMap::for($location->fresh())->pins() as $p) {
            $this->assertNull($p['venue_url']);
        }
    }
}
