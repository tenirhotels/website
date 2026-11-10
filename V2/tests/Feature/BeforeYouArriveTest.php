<?php

namespace Tests\Feature;

use App\Models\Faq;
use App\Models\Hotel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BeforeYouArriveTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    private function importAndMove(): Hotel
    {
        (require database_path('migrations/2026_11_06_000300_import_legacy_site_content.php'))->up();
        (require database_path('migrations/2026_11_08_000100_getting_here_dedupe.php'))->up();
        (require database_path('migrations/2026_11_08_000300_getting_here_no_faq_text.php'))->up();
        (require database_path('migrations/2026_11_10_001100_location_arrival_info.php'))->up();
        (require database_path('migrations/2026_11_10_001200_faq_order.php'))->up();

        return Hotel::where('slug', 'shymbulak')->firstOrFail();
    }

    public function test_page_shows_info_faq_and_schema_in_all_locales(): void
    {
        $this->importAndMove();
        foreach (['ru' => 'Multipass', 'en' => 'Multipass', 'kk' => 'Multipass', 'zh' => 'Multipass'] as $l => $needle) {
            $this->get("/$l/before-you-arrive")->assertOk()->assertSee($needle);
        }
        $this->get('/ru/before-you-arrive')
            ->assertSee('Частые вопросы')
            ->assertSee('Что такое Multipass?')
            ->assertSee('"@type":"FAQPage"', false)
            ->assertSee('С каждой станцией');
    }

    public function test_info_block_moves_out_of_getting_here_and_is_idempotent(): void
    {
        $hotel = $this->importAndMove();
        (require database_path('migrations/2026_11_10_001100_location_arrival_info.php'))->up();
        $hotel->refresh();
        $this->assertStringContainsString('Что важно знать', $hotel->arrival_info['ru']);
        $this->assertStringNotContainsString('Что важно знать', (string) $hotel->getting_here['ru']);
        $this->get('/ru/getting-here')->assertOk()->assertSee('/before-you-arrive', false)->assertDontSee('Частые вопросы');
    }

    public function test_faq_order_puts_cable_car_first_and_discount_last(): void
    {
        $hotel = $this->importAndMove();
        $q = Faq::where('hotel_id', $hotel->id)->where('is_published', true)->orderBy('sort')->get()->map(fn ($f) => $f->question['ru']);
        $this->assertSame('Входит ли канатная дорога в стоимость проживания?', $q->first());
        $this->assertSame('Есть ли скидка при длительном проживании?', $q->last());
    }

    public function test_unpublished_faq_is_hidden_and_legacy_information_redirects(): void
    {
        $hotel = $this->importAndMove();
        Faq::where('hotel_id', $hotel->id)->update(['is_published' => false]);
        $this->get('/ru/before-you-arrive')->assertOk()->assertDontSee('Частые вопросы')->assertDontSee('FAQPage', false);
        $this->get('/ru-ru/information')->assertRedirect('/ru/before-you-arrive');
    }
}
