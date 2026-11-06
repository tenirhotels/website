<?php

namespace Tests\Feature;

use App\Models\Experience;
use App\Models\Hotel;
use App\Models\Offer;
use App\Models\Page;
use App\Models\RoomType;
use App\Filament\Pages\SiteSettings;
use App\Filament\Resources\Hotels\Pages\EditHotel;
use App\Filament\Resources\Pages\Pages\CreatePage;
use App\Models\Setting;
use App\Models\User;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_root_redirects_by_browser_language(): void
    {
        $this->get('/', ['Accept-Language' => 'kk-KZ,kk;q=0.9'])->assertRedirect('/kk');
        $this->get('/', ['Accept-Language' => 'de-DE'])->assertRedirect('/kk');
        $this->get('/', ['Accept-Language' => 'ru-RU,ru;q=0.9,en;q=0.8'])->assertRedirect('/ru');
        $this->get('/', ['Accept-Language' => 'en;q=0.5,zh-CN;q=0.9'])->assertRedirect('/zh');
        $this->get('/', ['Accept-Language' => 'uk-UA,uk;q=0.9'])->assertRedirect('/ru');
        $this->withUnencryptedCookie('tenir_lang', 'en')->get('/', ['Accept-Language' => 'ru-RU'])->assertRedirect('/en');
    }

    public function test_kazakh_is_the_first_language(): void
    {
        $this->assertSame('kk', array_key_first(\App\Support\Locales::all()));
        $this->assertSame('kk', \App\Support\Locales::default());
    }

    public function test_public_pages_are_cached_without_cookies_and_flushed_on_save(): void
    {
        $first = $this->get('/ru');
        $first->assertOk()->assertHeader('X-Page-Cache', 'MISS');
        $this->assertEmpty($first->headers->getCookies());
        $this->assertStringContainsString('s-maxage', $first->headers->get('Cache-Control'));

        $this->get('/ru')->assertHeader('X-Page-Cache', 'HIT');

        Page::query()->first()?->touch();
        Setting::put('seo', ['x' => 1]);
        $this->get('/ru')->assertHeader('X-Page-Cache', 'MISS');

        Setting::put('perf', ['page_cache' => false]);
        $this->get('/ru')->assertHeaderMissing('X-Page-Cache');
    }

    public function test_analytics_ids_from_settings_are_rendered_and_validated(): void
    {
        Setting::put('analytics', [
            'ga4' => 'G-ABCDE12345', 'gtm' => 'GTM-ABC1234', 'yandex_metrika' => '12345678',
            'meta_pixel' => '1234567890123456', 'verify_google' => 'tok123', 'custom_head' => '<meta name="x-custom" content="1">',
        ]);
        $this->get('/ru')->assertOk()
            ->assertSee('G-ABCDE12345', false)->assertSee('GTM-ABC1234', false)
            ->assertSee('ym(12345678', false)->assertSee('1234567890123456', false)
            ->assertSee('google-site-verification', false)->assertSee('x-custom', false);

        Setting::put('analytics', ['ga4' => '"><script>alert(1)</script>', 'meta_pixel' => 'abc']);
        $this->get('/ru')->assertOk()->assertDontSee('alert(1)', false)->assertDontSee('fbevents.js', false);
    }

    public function test_pages_have_canonical_hreflang_and_schema(): void
    {
        $this->get('/en/rooms/deluxe-mountain-view')
            ->assertOk()
            ->assertSee('<link rel="canonical" href="'.url('/en/rooms/deluxe-mountain-view').'">', false)
            ->assertSee('hreflang="kk" href="'.url('/kk/rooms/deluxe-mountain-view').'"', false)
            ->assertSee('hreflang="x-default" href="'.url('/kk/rooms/deluxe-mountain-view').'"', false);

        $this->get('/en')->assertSee('"@type":"Hotel"', false)->assertSee('"@type":"Organization"', false);
    }

    public function test_unknown_content_returns_404(): void
    {
        $this->get('/ru/destinations/nowhere')->assertNotFound();
        $this->get('/ru/rooms/nowhere')->assertNotFound();
        $this->get('/ru/no-such-page')->assertNotFound();
        $this->get('/de/about')->assertNotFound();
    }

    public function test_unpublished_content_is_hidden(): void
    {
        Offer::where('slug', 'long-stay')->update(['is_published' => false]);
        $this->get('/ru/offers/long-stay')->assertNotFound();

        Page::where('slug', 'about')->update(['is_published' => false]);
        $this->get('/ru/about')->assertNotFound();
    }

    public function test_booking_bar_params_are_translated_for_exely(): void
    {
        $in = now()->addDays(10)->toDateString();
        $out = now()->addDays(13)->toDateString();

        $this->get("/ru/booking?hotel=shymbulak&check_in=$in&check_out=$out&adults=2&children=1&promo_code=SKY")
            ->assertRedirect("/ru/booking?hotel=shymbulak&date=$in&nights=3&adults=2&children=1&promo-code=SKY");

        // Адрес после перенаправления открывается без нового редиректа (раньше был бесконечный цикл).
        $this->get("/ru/booking?hotel=shymbulak&date=$in&nights=3&adults=2&children=1&promo-code=SKY")->assertOk();
        $this->get('/ru/booking?adults=2&children=0')->assertOk();
        $this->get('/ru/booking?hotel=shymbulak')->assertOk();
    }

    public function test_booking_page_embeds_travelline_with_context_and_language(): void
    {
        $this->get('/kk/booking')
            ->assertOk()
            ->assertSee('"TL-INT-tenirhotels"', false)
            ->assertSee("['setContext', \"TL-INT-tenirhotels\", \"kk\"]", false)
            ->assertSee('tl-booking-form', false)
            ->assertSee('noindex', false);
    }

    public function test_room_and_offer_buttons_deep_link_into_exely(): void
    {
        $this->assertStringContainsString('room_type=62361', RoomType::first()->bookingUrl());
        $this->get(RoomType::first()->bookingUrl())->assertRedirect();
        $this->assertStringContainsString('special_offer=295761', Offer::where('slug', 'long-stay')->first()->bookingUrl());
    }

    public function test_legacy_urls_redirect_and_keep_query(): void
    {
        $this->get('/en-gb/booking/?room-type=62361')->assertRedirect('/en/booking?room-type=62361')->assertStatus(301);
        $this->get('/ru-ru/special-offers/')->assertRedirect('/ru/offers');
        $this->get('/hotel')->assertRedirect('/ru');
        $this->get('/activities')->assertRedirect('/ru/experiences');

        $this->assertDatabaseHas('redirects', ['from_path' => '/hotel', 'hits' => 1]);
    }

    public function test_sitemap_lists_every_language_with_alternates(): void
    {
        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml')
            ->assertSee('<loc>'.url('/zh/experiences/tenir-sky').'</loc>', false)
            ->assertSee('hreflang="zh-Hans"', false)
            ->assertDontSee('/booking</loc>', false);
    }

    public function test_translation_fallback_chain(): void
    {
        $exp = Experience::where('slug', 'tenir-sky')->first();

        $this->assertSame('Tenir Sky — жұлдыздарға ең жақын', $exp->t('title', 'kk'));
        $this->assertSame($exp->t('summary', 'ru'), $exp->t('summary', 'kk'));   // kk → ru
        $this->assertSame($exp->t('summary', 'en'), $exp->t('summary', 'zh'));   // zh → en
    }

    public function test_admin_pages_render(): void
    {
        $this->actingAs(User::factory()->create());

        foreach (['hotels', 'room-types', 'offers', 'experiences', 'venues', 'pages', 'stories', 'faqs', 'redirects'] as $resource) {
            $this->get("/admin/$resource")->assertOk();
            $this->get("/admin/$resource/create")->assertOk();
        }

        $this->get('/admin/hotels/'.Hotel::first()->id.'/edit')->assertOk()->assertSee('Tenir Shymbulak');
        $this->get('/admin/pages/'.Page::where('slug', 'about')->first()->id.'/edit')->assertOk();
        $this->get('/admin/site-settings')->assertOk();
    }

    public function test_admin_saves_translations_blocks_and_settings(): void
    {
        $this->actingAs(User::factory()->create());
        $hotel = Hotel::first();

        Livewire::test(EditHotel::class, ['record' => $hotel->id])
            ->fillForm(['name.kk' => 'Tenir Шымбұлақ', 'tagline.zh' => '离天空更近'])
            ->call('save')
            ->assertHasNoFormErrors();
        $hotel->refresh();
        $this->assertSame('Tenir Шымбұлақ', $hotel->t('name', 'kk'));
        $this->assertSame('Tenir Shymbulak', $hotel->t('name', 'ru'));
        $this->assertSame('离天空更近', $hotel->t('tagline', 'zh'));

        Livewire::test(CreatePage::class)
            ->fillForm([
                'slug' => 'wellness',
                'title.ru' => 'Велнес',
                'content.ru' => [['type' => 'quote', 'data' => ['quote' => 'Тишина лечит', 'caption' => null]]],
            ])
            ->call('create')
            ->assertHasNoFormErrors();
        $this->get('/ru/wellness')->assertOk()->assertSee('Тишина лечит');

        Livewire::test(CreatePage::class)
            ->fillForm(['slug' => 'offers', 'title.ru' => 'Занято'])
            ->call('create')
            ->assertHasFormErrors(['slug']);

        Livewire::test(SiteSettings::class)
            ->fillForm(['contacts.phone' => '+7 700 000 00 00'])
            ->call('save')
            ->assertHasNoFormErrors();
        $this->assertSame('+7 700 000 00 00', Setting::get('contacts.phone'));
        $this->assertSame('reservations@tenirhotels.com', Setting::get('contacts.email'));
    }
}
