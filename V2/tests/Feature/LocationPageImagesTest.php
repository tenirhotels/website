<?php

namespace Tests\Feature;

use App\Models\Hotel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocationPageImagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_pages_use_own_images_from_location(): void
    {
        $this->seed();
        $hotel = Hotel::query()->firstOrFail();
        $hotel->location->update([
            'getting_image' => 'locations/pages/getting-test.jpg',
            'arrival_image' => 'locations/pages/arrival-test.jpg',
        ]);
        $hotel = Hotel::query()->firstOrFail();

        $this->assertSame('locations/pages/getting-test.jpg', $hotel->gettingImage());
        $this->assertSame('locations/pages/arrival-test.jpg', $hotel->arrivalImage());
        $this->get($hotel->sectionUrl('getting-here'))->assertOk()->assertSee('getting-test', false);
        $this->get($hotel->sectionUrl('before-you-arrive'))->assertOk()->assertSee('arrival-test', false);
    }

    public function test_images_differ_by_default_when_gallery_has_several(): void
    {
        $this->seed();
        $hotel = Hotel::query()->firstOrFail();
        $hotel->update(['gallery' => ['a.jpg', 'b.jpg', 'c.jpg']]);
        $hotel = Hotel::query()->firstOrFail();

        $this->assertSame('a.jpg', $hotel->gettingImage());
        $this->assertSame('c.jpg', $hotel->arrivalImage());
    }
}
