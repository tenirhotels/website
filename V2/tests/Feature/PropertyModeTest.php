<?php

namespace Tests\Feature;

use App\Filament\Resources\Offers\Pages\CreateOffer;
use App\Models\Hotel;
use App\Models\Offer;
use App\Models\Setting;
use App\Models\User;
use App\Support\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Режимы сайта: один отель (короткие адреса) ↔ сеть (/destinations/{отель}/...).
 */
class PropertyModeTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    private const SINGLE_PATHS = [
        '', 'rooms', 'rooms/deluxe-mountain-view', 'dining', 'gallery', 'getting-here', 'before-you-arrive', 'reviews',
        'experiences', 'experiences/tenir-sky', 'offers', 'offers/long-stay',
        'stories', 'contact', 'about', 'sustainability', 'booking',
    ];

    private const MULTI_PATHS = [
        '', 'destinations', 'destinations/shymbulak', 'destinations/shymbulak/rooms',
        'destinations/shymbulak/rooms/deluxe-mountain-view', 'destinations/shymbulak/dining',
        'destinations/shymbulak/experiences', 'destinations/shymbulak/offers',
        'destinations/shymbulak/gallery', 'destinations/shymbulak/getting-here', 'destinations/shymbulak/before-you-arrive', 'destinations/shymbulak/reviews',
        'destinations/almaty', 'experiences', 'experiences/tenir-sky', 'offers', 'offers/long-stay',
        'stories', 'contact', 'about', 'sustainability', 'booking',
    ];

    private function addSecondHotel(string $status = Hotel::STATUS_OPEN): Hotel
    {
        return Hotel::create([
            'slug' => 'almaty', 'name' => ['ru' => 'Tenir Almaty', 'en' => 'Tenir Almaty'],
            'summary' => ['ru' => 'Городской отель Tenir.'], 'status' => $status,
            'opening_label' => ['ru' => 'Открытие в 2028'], 'is_featured' => false,
            'is_published' => true, 'sort' => 2,
        ]);
    }

    // ---------------------------------------------------------------- один отель

    public function test_one_published_hotel_means_single_mode(): void
    {
        $this->assertTrue(Property::isSingle());
        $this->assertSame('shymbulak', Property::primary()->slug);
        $this->assertSame(url('/ru'), Hotel::first()->url('ru'));
        $this->assertSame(url('/en/rooms'), Hotel::first()->sectionUrl('rooms', [], 'en'));
    }

    public function test_single_mode_pages_render_in_every_language(): void
    {
        foreach (['ru', 'kk', 'en', 'zh'] as $locale) {
            foreach (self::SINGLE_PATHS as $path) {
                $this->get("/$locale/$path")->assertOk();
            }
        }
    }

    public function test_single_mode_home_is_the_hotel(): void
    {
        $this->get('/ru')
            ->assertOk()
            ->assertSee('"@type":"Hotel"', false)
            ->assertSee('href="'.url('/ru/rooms').'"', false)
            ->assertSee('href="'.url('/ru/dining').'"', false)
            ->assertDontSee('/ru/destinations', false);

        // Подменю отеля не нужно — разделы уже в главном меню.
        $this->get('/ru/rooms')->assertOk()->assertDontSee('aria-label="Tenir Shymbulak" class="sticky', false);
    }

    public function test_single_mode_redirects_long_urls_to_short(): void
    {
        $this->get('/ru/destinations')->assertRedirect('/ru')->assertStatus(301);
        $this->get('/ru/destinations/shymbulak')->assertRedirect('/ru')->assertStatus(301);
        $this->get('/en/destinations/shymbulak/rooms')->assertRedirect('/en/rooms');
        $this->get('/en/destinations/shymbulak/rooms/deluxe-mountain-view')->assertRedirect('/en/rooms/deluxe-mountain-view');
        $this->get('/kk/destinations/shymbulak/getting-here')->assertRedirect('/kk/getting-here');
        $this->get('/ru/destinations/shymbulak/offers')->assertRedirect('/ru/offers');
    }

    public function test_single_mode_sitemap_uses_short_urls(): void
    {
        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertSee('<loc>'.url('/ru/rooms/deluxe-mountain-view').'</loc>', false)
            ->assertSee('<loc>'.url('/zh/getting-here').'</loc>', false)
            ->assertDontSee('/destinations', false);
    }

    public function test_legacy_exely_urls_land_on_short_urls(): void
    {
        $this->get('/en-gb/rooms/')->assertRedirect('/en/rooms');
        $this->get('/ru-ru/restaurants')->assertRedirect('/ru/dining');
        $this->get('/hotel')->assertRedirect('/ru');
    }

    public function test_admin_shows_location_and_defaults_to_the_primary_hotel(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get('/admin/offers/create')->assertOk()->assertSee('Локация (отель)');
        $this->get('/admin/room-types/create')->assertOk()->assertSee('Локация (отель)');

        \Illuminate\Support\Facades\Storage::fake('public');
        Livewire::test(CreateOffer::class)
            ->fillForm(['slug' => 'ski-week', 'title.ru' => 'Лыжная неделя', 'summary.ru' => 'Кратко', 'description.ru' => '<p>Полное описание</p>', 'image' => [\Illuminate\Http\UploadedFile::fake()->image('offer.jpg')]])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertSame(Hotel::first()->id, Offer::where('slug', 'ski-week')->first()->hotel_id);
    }

    public function test_hotel_card_has_tabs_for_its_rooms_venues_offers_and_experiences(): void
    {
        $this->actingAs(User::factory()->create());
        $hotel = Hotel::first();

        Livewire::test(\App\Filament\Resources\Hotels\RelationManagers\RoomsRelationManager::class, [
            'ownerRecord' => $hotel, 'pageClass' => \App\Filament\Resources\Hotels\Pages\EditHotel::class,
        ])->assertSuccessful()->assertCanSeeTableRecords($hotel->rooms);

        $this->get('/admin/hotels/'.$hotel->id.'/edit')->assertOk()
            ->assertSee('Номера')->assertSee('Рестораны и бары')->assertSee('Предложения')->assertSee('Впечатления');
    }

    // ---------------------------------------------------------------- сеть

    public function test_second_published_hotel_switches_to_multi_mode(): void
    {
        $this->addSecondHotel();

        $this->assertTrue(Property::isMulti());
        $this->assertSame(url('/ru/destinations/shymbulak'), Hotel::where('slug', 'shymbulak')->first()->url('ru'));
    }

    public function test_coming_soon_hotel_also_switches_to_multi_mode(): void
    {
        $this->addSecondHotel(Hotel::STATUS_COMING_SOON);

        $this->assertTrue(Property::isMulti());
        $this->get('/ru/destinations')->assertOk()->assertSee('Tenir Almaty')->assertSee('Открытие в 2028');
    }

    public function test_unpublished_second_hotel_keeps_single_mode(): void
    {
        $this->addSecondHotel()->update(['is_published' => false]);

        $this->assertTrue(Property::isSingle());
        $this->get('/ru/rooms')->assertOk();
    }

    public function test_multi_mode_pages_render_in_every_language(): void
    {
        $this->addSecondHotel();

        foreach (['ru', 'kk', 'en', 'zh'] as $locale) {
            foreach (self::MULTI_PATHS as $path) {
                $this->get("/$locale/$path")->assertOk();
            }
        }
    }

    public function test_multi_mode_home_is_the_brand_showcase(): void
    {
        $this->addSecondHotel();

        $this->get('/ru')
            ->assertOk()
            ->assertSee('Tenir Shymbulak')
            ->assertSee('Tenir Almaty')
            ->assertSee('href="'.url('/ru/destinations').'"', false)
            ->assertSee('tl-search-form', false)     // плашка бронирования — виджет Exely
            ->assertDontSee('"@type":"Hotel"', false);

        $this->get('/ru/destinations/shymbulak')
            ->assertOk()
            ->assertSee('"@type":"Hotel"', false)
            ->assertSee('href="'.url('/ru/destinations/shymbulak/rooms').'"', false);
    }

    public function test_multi_mode_redirects_short_urls_to_primary_hotel(): void
    {
        $this->addSecondHotel();

        $this->get('/ru/rooms')->assertRedirect('/ru/destinations/shymbulak/rooms')->assertStatus(301);
        $this->get('/en/rooms/deluxe-mountain-view')->assertRedirect('/en/destinations/shymbulak/rooms/deluxe-mountain-view');
        $this->get('/kk/dining')->assertRedirect('/kk/destinations/shymbulak/dining');
        $this->get('/ru/getting-here')->assertRedirect('/ru/destinations/shymbulak/getting-here');
    }

    public function test_multi_mode_hubs_filter_by_hotel(): void
    {
        $almaty = $this->addSecondHotel();
        Offer::create(['hotel_id' => $almaty->id, 'slug' => 'city-break', 'title' => ['ru' => 'Городской уикенд'], 'is_published' => true]);

        $this->get('/ru/offers')->assertSee('Городской уикенд')->assertSee('Длительное проживание');
        $this->get('/ru/offers?hotel=almaty')->assertSee('Городской уикенд')->assertDontSee('Длительное проживание');
        $this->get('/ru/offers?hotel=shymbulak')->assertDontSee('Городской уикенд');
    }

    public function test_multi_mode_sitemap_uses_destination_urls(): void
    {
        $this->addSecondHotel();

        $this->get('/sitemap.xml')
            ->assertSee('<loc>'.url('/ru/destinations/shymbulak/rooms/deluxe-mountain-view').'</loc>', false)
            ->assertSee('<loc>'.url('/en/destinations/almaty').'</loc>', false)
            ->assertDontSee('<loc>'.url('/ru/rooms').'</loc>', false);
    }

    // ---------------------------------------------------------------- ручной режим

    public function test_mode_can_be_forced_in_settings(): void
    {
        $this->addSecondHotel();
        Setting::put('site', ['mode' => 'single']);

        $this->assertTrue(Property::isSingle());
        $this->get('/ru/rooms')->assertOk();
        $this->get('/ru/destinations/shymbulak')->assertRedirect('/ru');
        $this->get('/ru/destinations/almaty')->assertOk();   // второй отель остаётся доступен

        Setting::put('site', ['mode' => 'multi']);
        Hotel::where('slug', 'almaty')->update(['is_published' => false]);
        Property::flush();

        $this->assertTrue(Property::isMulti());
        $this->get('/ru/destinations/shymbulak/rooms')->assertOk();
    }
}
