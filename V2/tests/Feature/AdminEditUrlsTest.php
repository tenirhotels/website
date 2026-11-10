<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminEditUrlsTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_every_record_edit_page_opens(): void
    {
        $this->actingAs(User::factory()->create());
        $resources = [
            \App\Filament\Resources\Hotels\HotelResource::class,
            \App\Filament\Resources\RoomTypes\RoomTypeResource::class,
            \App\Filament\Resources\Venues\VenueResource::class,
            \App\Filament\Resources\Transports\TransportResource::class,
            \App\Filament\Resources\Offers\OfferResource::class,
            \App\Filament\Resources\Experiences\ExperienceResource::class,
            \App\Filament\Resources\Stories\StoryResource::class,
            \App\Filament\Resources\Pages\PageResource::class,
            \App\Filament\Resources\Faqs\FaqResource::class,
            \App\Filament\Resources\Redirects\RedirectResource::class,
        ];
        $checked = 0;
        foreach ($resources as $resource) {
            foreach ($resource::getModel()::limit(3)->get() as $record) {
                $url = $resource::getUrl('edit', ['record' => $record]);
                $this->get($url)->assertOk();
                $checked++;
            }
        }
        $this->assertGreaterThan(10, $checked);

        // Ссылки в списках ведут по id, а не по слагу.
        $venue = \App\Models\Venue::first();
        $this->get('/admin/venues')->assertOk()->assertSee('/admin/venues/'.$venue->id.'/edit', false);
        $hotel = \App\Models\Hotel::first();
        $this->get('/admin/hotels')->assertOk()->assertSee('/admin/hotels/'.$hotel->id.'/edit', false);
    }
}
