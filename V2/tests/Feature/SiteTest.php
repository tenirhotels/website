<?php

namespace Tests\Feature;

use App\Models\Experience;
use App\Models\Faq;
use App\Models\Hotel;
use App\Models\Offer;
use App\Models\Page;
use App\Models\RoomType;
use App\Filament\Pages\SiteSettings;
use App\Filament\Resources\Hotels\Pages\EditHotel;
use App\Filament\Resources\Pages\Pages\CreatePage;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
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

    public function test_exely_lang_prefixed_urls_redirect_with_query(): void
    {
        $this->get('/lang/en/booking?date=2026-11-10&nights=1&adults=2&children=&children-age=')
            ->assertRedirect('/en/booking?adults=2&children=&children-age=&date=2026-11-10&nights=1');
        $this->get('/lang/xx/booking')->assertNotFound();
    }

    public function test_recognition_block_shows_published_awards_with_sources(): void
    {
        $this->get('/en')->assertOk()
            ->assertSee('One MICHELIN Key')
            ->assertSee('guide.michelin.com', false)
            ->assertSee('"award":', false);

        \App\Models\Recognition::query()->get()->each->update(['is_published' => false]);
        $this->get('/en')->assertOk()->assertDontSee('One MICHELIN Key');
    }

    public function test_cable_block_follows_hotel_setting_not_site_setting(): void
    {
        $hotel = Hotel::where('slug', 'shymbulak')->first();
        $this->assertTrue($hotel->hasCable());
        $url = $hotel->sectionUrl('getting-here', [], 'ru');
        \App\Models\Transport::where('location_id', $hotel->location_id)->where('code', 'M-1')->update(['hours' => '08:30–19:00']);
        \App\Support\PageCache::flush();
        \App\Support\Property::flush(); // в тесте всё живёт в одном процессе
        $this->get($url)->assertOk()->assertSee('08:30–19:00')->assertSee('M-1');

        \App\Models\Transport::where('location_id', $hotel->location_id)->update(['is_published' => false]);
        \App\Support\PageCache::flush();
        \App\Support\Property::flush(); // в тесте всё живёт в одном процессе
        $this->get($url)->assertOk()->assertDontSee('M-1')->assertDontSee('08:30–19:00');
    }

    public function test_cable_names_migration_renames_combi_to_k_lifts(): void
    {
        (require database_path('migrations/2026_11_06_000300_import_legacy_site_content.php'))->up();
        $text = function (): string {
            $out = '';
            foreach (['pages' => 'content', 'locations' => 'getting_here', 'faqs' => 'answer'] as $table => $col) {
                foreach (\Illuminate\Support\Facades\DB::table($table)->pluck($col) as $raw) {
                    $d = json_decode((string) $raw, true);
                    $out .= is_array($d) ? json_encode($d, JSON_UNESCAPED_UNICODE) : (string) $raw;
                }
            }

            return $out;
        };
        $this->assertMatchesRegularExpression('/Комби-\d|Combi-\d/u', $text());

        (require database_path('migrations/2026_11_10_000100_cable_names_m1_k1_k2.php'))->up();

        $this->assertDoesNotMatchRegularExpression('/Комби|Combi/u', $text());
        $this->assertStringContainsString('K-1', $text());
    }

    public function test_legacy_site_content_import_fills_placeholders_and_keeps_edits(): void
    {
        $hotel = Hotel::where('slug', 'shymbulak')->first();
        $offer = Offer::where('slug', 'long-stay')->first();
        $this->assertSame(3, (int) $offer->min_nights);

        $migration = require database_path('migrations/2026_11_06_000300_import_legacy_site_content.php');
        $migration->up();
        $migration->up(); // идемпотентно

        $this->get('/ru/getting-here')->assertOk()->assertSee('Комби-1')->assertSee('Multipass')->assertDontSee('Частые вопросы');
        $this->assertTrue(\App\Models\Faq::where('is_published', true)->exists()); // вопросы остаются в данных, на странице их нет
        $this->get('/kk/getting-here')->assertOk()->assertSee('Комби-2');
        $this->get('/ru/experiences/welcome-tour')->assertOk()->assertSee('сертифицированными гидами')->assertDontSee('Дополните');
        $this->get('/ru/dining')->assertOk()->assertDontSee('Опишите')->assertSee('Самый высокогорный ресторан');
        $this->get('/ru/contact')->assertOk()->assertSee('sales@tenirhotels.com');
        $this->assertSame(4, (int) $offer->fresh()->min_nights);
        $this->assertSame(1, Faq::where('hotel_id', $hotel->id)->get()->filter(fn ($f) => ($f->question['ru'] ?? '') === 'Что такое Multipass?')->count());

        // Правка редактора не перезаписывается.
        $hotel->update(['getting_here' => ['ru' => str_repeat('Свой длинный текст редактора. ', 30)]]);
        $migration->up();
        $this->assertStringContainsString('Свой длинный текст', $hotel->fresh()->getting_here['ru']);
    }

    public function test_location_owns_place_settings_and_hotel_reads_them(): void
    {
        $hotel = Hotel::where('slug', 'shymbulak')->first();
        $loc = $hotel->location;
        $this->assertNotNull($loc);
        $this->assertSame($loc->t('address'), $hotel->t('address'));
        $this->assertSame(3200, (int) $hotel->altitude_m);

        $loc->update(['address' => ['ru' => 'Новый адрес локации']]);
        \App\Support\PageCache::flush();
        \App\Support\Property::flush();
        $this->assertSame('Новый адрес локации', Hotel::find($hotel->id)->t('address'));
        $this->get($hotel->sectionUrl('getting-here', [], 'ru'))->assertOk();
        $this->assertTrue(Schema::hasColumn('hotels', 'location_id'));
        $this->assertFalse(Schema::hasColumn('hotels', 'address'));
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
    public function test_resort_map_page_in_all_languages(): void
    {
        foreach (['kk' => 'Курорт картасы', 'ru' => 'Карта курорта', 'en' => 'Resort map', 'zh' => '度假区地图'] as $locale => $title) {
            $this->get("/$locale/map")->assertOk()->assertSee($title)->assertSee('data-pin="r1"', false)->assertSee('data-pin="st-combi1"', false);
        }
        $this->get('/ru/map')->assertSee('≈22 мин', false)->assertSee('↑ Вверх (K-1): Средняя', false)->assertSee('↓ Вниз (M-1): Медеу', false)->assertSee('Верёвочный парк Медеу')->assertSee('Лестница здоровья');
        $this->get('/en/map')->assertSee('Rope park Medeu')->assertSee('Cable cars');
        $this->get('/ru/map')->assertDontSee('z-30 bg-white px-4 pb-5', false);
        $this->get('/ru/contact')->assertSee('z-30 bg-white px-4 pb-5', false);
        $this->get('/sitemap.xml')->assertSee('/ru/map', false);
        $this->assertFileExists(public_path('images/map/winter.webp'));
        $this->assertFileExists(public_path('images/map/summer.webp'));

        $this->assertCount(3, \App\Support\ResortMap::geoPoints());
        $this->assertSame('winter', \App\Support\ResortMap::defaultSeason(new \DateTimeImmutable('2026-12-15')));
        $this->assertSame('summer', \App\Support\ResortMap::defaultSeason(new \DateTimeImmutable('2026-07-15')));
    }

    public function test_gift_certificates_from_gifter(): void
    {
        config(['tenir.gifts.url' => 'https://gifts.example.test']);
        $offer = fn (int $id, string $ru, bool $open = true) => [
            'id' => $id, 'name' => ['ru' => $ru, 'en' => $ru.' EN', 'kk' => $ru.' KK'], 'description' => ['ru' => 'Описание'],
            'validity' => ['ru' => '3 месяца с даты продажи', 'en' => '3 months', 'kk' => '3 ай'], 'price' => 50000, 'currency' => 'KZT',
            'image' => null, 'sale' => $open ? 'open' : 'ended', 'open' => $open, 'url' => 'https://gifts.example.test/order/'.$id,
        ];
        Http::fake(['gifts.example.test/api/offers' => Http::sequence()
            ->push(['offers' => [$offer(7, 'Романтический вечер'), $offer(8, 'Закрытый', false)]])
            ->whenEmpty(Http::response('', 500))]);
        \Illuminate\Support\Facades\Cache::forget('gifts.offers');

        $this->get('/ru/gifts')->assertOk()->assertSee('Романтический вечер')->assertSee('50 000')
            ->assertSee('https://gifts.example.test/order/7?lang=ru', false)->assertDontSee('Закрытый');
        $this->get('/kk/gifts')->assertSee('Романтический вечер KK')->assertSee('lang=kk', false);
        $this->get('/zh/gifts')->assertSee('Романтический вечер EN')->assertSee('lang=en', false);
        // на главной блока по умолчанию нет, включается настройкой
        $lead = __('gifts.lead', [], 'ru');
        $this->get('/ru')->assertDontSee($lead, false);
        Setting::put('gifts', ['on_home' => true]);
        $this->get('/ru')->assertSee($lead, false);
        $this->get('/sitemap.xml')->assertSee('/ru/gifts', false);

        // gifter недоступен — остаётся последняя копия
        $this->assertNull(\App\Support\Gifts::sync());
        $this->assertCount(1, \App\Support\Gifts::offers('ru'));

        // выключено в админке — раздела нет
        Setting::put('gifts', ['enabled' => false, 'on_home' => true]);
        $this->get('/ru/gifts')->assertNotFound();
        $this->get('/ru')->assertDontSee($lead, false);
    }

    public function test_footer_has_social_strip_brand_and_legal_links(): void
    {
        $html = $this->get('/ru')->assertOk()->getContent();
        foreach (['instagram.com/tenir.ecohotels', 'youtube.com/@tenirhotels', 'tiktok.com/@tenirhotels', 'soundcloud.com/tenirhotels'] as $url) {
            $this->assertStringContainsString($url, $html);
        }
        $this->assertStringNotContainsString('facebook.com/tenirhotels', $html);
        $this->get('/ru')->assertSee('Наша философия')->assertSee('Политика конфиденциальности')->assertSee('Карта курорта');
        $this->get('/en')->assertSee('Our philosophy');
    }

    public function test_events_calendar_shows_upcoming_and_hides_past(): void
    {
        $base = ['cta' => 'sales', 'is_published' => true, 'summary' => ['ru' => 'Описание']];
        \App\Models\Event::create($base + ['slug' => 'new-year', 'title' => ['ru' => 'Новогодний ужин'], 'starts_on' => now()->addDays(10), 'ends_on' => now()->addDays(12), 'time_text' => '20:00']);
        \App\Models\Event::create($base + ['slug' => 'old-party', 'title' => ['ru' => 'Прошлая вечеринка'], 'starts_on' => now()->subDays(20), 'cta' => 'booking']);

        $this->get('/ru/events')->assertOk()->assertSee('Новогодний ужин')->assertDontSee('Прошлая вечеринка');
        $this->get('/ru/events/new-year')->assertOk()->assertSee('Запрос на мероприятие', false)->assertSee('sales@tenirhotels.com', false);
        $this->get('/ru/events/old-party')->assertOk()->assertSee('уже прошло', false);
        $this->get('/ru')->assertOk()->assertSee('Новогодний ужин');
        $this->get('/sitemap.xml')->assertSee('/ru/events/new-year', false);
    }

    public function test_hotel_hero_follows_season(): void
    {
        $hotel = new \App\Models\Hotel(['hero_image' => 'w.jpg', 'hero_image_summer' => 's.jpg']);
        $this->travelTo(now()->setDate(2026, 1, 15));
        $this->assertSame('w.jpg', $hotel->seasonHero());
        $this->travelTo(now()->setDate(2026, 7, 15));
        $this->assertSame('s.jpg', $hotel->seasonHero());
        $this->assertSame('w.jpg', (new \App\Models\Hotel(['hero_image' => 'w.jpg']))->seasonHero());
    }
}
