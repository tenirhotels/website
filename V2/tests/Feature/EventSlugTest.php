<?php

namespace Tests\Feature;

use App\Filament\Resources\Events\Pages\CreateEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EventSlugTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_slug_is_built_from_title_and_month_in_either_order(): void
    {
        $this->actingAs(User::factory()->create());
        $src = \App\Support\Locales::source();

        // Сначала дата, потом название.
        Livewire::test(CreateEvent::class)
            ->fillForm(['starts_on' => '2026-11-15'])
            ->fillForm(["title.$src" => 'Mountain Days'])
            ->assertFormSet(['slug' => 'mountain-days-2026-11']);

        // Сначала название, потом дата.
        Livewire::test(CreateEvent::class)
            ->fillForm(["title.$src" => 'Mountain Days'])
            ->fillForm(['starts_on' => '2026-12-03'])
            ->assertFormSet(['slug' => 'mountain-days-2026-12']);
    }

    public function test_event_month_labels_follow_site_language(): void
    {
        $e = new \App\Models\Event(['starts_on' => '2026-12-31', 'ends_on' => '2027-01-02']);
        foreach (['en' => 'December – January', 'ru' => 'декабрь – январь'] as $loc => $expected) {
            app()->setLocale($loc);
            \Carbon\Carbon::setLocale($loc);
            $this->assertSame($expected, $e->monthBadge());
            $this->assertSame('31–2', $e->dayBadge());
        }
    }
}
