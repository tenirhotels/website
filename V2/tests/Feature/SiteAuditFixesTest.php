<?php

namespace Tests\Feature;

use App\Models\Faq;
use App\Models\Hotel;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteAuditFixesTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    private function applyMigrations(): void
    {
        (require database_path('migrations/2026_11_06_000300_import_legacy_site_content.php'))->up();
        (require database_path('migrations/2026_11_08_000200_unify_cable_times.php'))->up();
        (require database_path('migrations/2026_11_10_001400_site_audit_fixes.php'))->up();
    }

    public function test_no_faq_says_tickets_arrive_on_the_day_of_arrival(): void
    {
        $this->applyMigrations();
        $hotel = Hotel::where('slug', 'shymbulak')->first();
        // Редактор мог вернуть старую формулировку — миграция приводит её к «за сутки до заезда».
        $faq = Faq::where('hotel_id', $hotel->id)->get()->first(fn ($f) => ($f->question['ru'] ?? '') === 'Что такое Multipass?');
        $faq->update(['answer' => ['ru' => '<p>Электронный билет приходит в день заезда.</p>', 'en' => '<p>The e-ticket arrives on the day of arrival.</p>', 'kk' => '<p>Электронды билет келген күні жіберіледі.</p>']]);
        (require database_path('migrations/2026_11_10_001400_site_audit_fixes.php'))->up();
        $faq->refresh();
        $this->assertStringContainsString('за сутки до заезда', $faq->answer['ru']);
        $this->assertStringContainsString('the day before arrival', $faq->answer['en']);
        $this->assertStringContainsString('бір тәулік', $faq->answer['kk']);
        foreach (Faq::all() as $f) {
            $this->assertStringNotContainsString('в день заезда', json_encode($f->answer, JSON_UNESCAPED_UNICODE));
        }
    }

    public function test_kazakh_gaps_are_filled_and_editor_text_is_kept(): void
    {
        $this->applyMigrations();
        $hotel = Hotel::where('slug', 'shymbulak')->first();
        $this->assertStringContainsString('Іле Алатауының', $hotel->summary['kk']);
        $this->assertNotEmpty($hotel->rooms()->first()->amenities['kk'] ?? null);
        $faq = Faq::where('hotel_id', $hotel->id)->get()->first(fn ($f) => ($f->question['ru'] ?? '') === 'Есть ли скидка при длительном проживании?');
        $this->assertSame('Ұзақ тұрғанда жеңілдік бар ма?', $faq->question['kk']);

        // Уже заполненный язык не перезаписывается; повторный запуск ничего не ломает.
        $hotel->update(['hero_line' => ['en' => 'the highest-altitude eco hotel in Kazakhstan', 'kk' => 'Свой текст']]);
        (require database_path('migrations/2026_11_10_001400_site_audit_fixes.php'))->up();
        $this->assertSame('Свой текст', $hotel->fresh()->hero_line['kk']);
        $this->assertSame('Самый высокогорный эко-отель Казахстана', $hotel->fresh()->hero_line['ru']);
    }

    public function test_legal_pages_have_no_placeholder(): void
    {
        $this->applyMigrations();
        foreach (['ru', 'kk', 'en', 'zh'] as $l) {
            foreach (['privacy', 'terms'] as $slug) {
                $this->get("/$l/$slug")->assertOk()->assertDontSee('[Вставьте')->assertDontSee('[Paste')->assertSee('reservations@tenirhotels.com', false);
            }
        }
        $page = Page::where('slug', 'terms')->first();
        $page->update(['content' => ['ru' => [['type' => 'text', 'data' => ['heading' => null, 'body' => '<p>Настоящий текст условий.</p>']]]]]);
        (require database_path('migrations/2026_11_10_001400_site_audit_fixes.php'))->up();
        $this->assertStringContainsString('Настоящий текст условий', $page->fresh()->content['ru'][0]['data']['body']);
    }

    public function test_block_image_falls_back_to_other_language(): void
    {
        $page = Page::create(['slug' => 'imgtest', 'title' => ['ru' => 'T', 'kk' => 'T'], 'placement' => 'none', 'is_published' => true, 'content' => [
            'ru' => [['type' => 'image_text', 'data' => ['heading' => 'a', 'body' => 'b', 'image' => 'pages/x.webp']]],
            'kk' => [['type' => 'image_text', 'data' => ['heading' => 'a', 'body' => 'b', 'image' => null]]],
        ]]);
        $this->assertSame('pages/x.webp', $page->blocks('kk')[0]['data']['image']);
        $this->assertSame('pages/x.webp', $page->blocks('ru')[0]['data']['image']);
    }
}
