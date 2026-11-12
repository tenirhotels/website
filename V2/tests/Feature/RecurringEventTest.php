<?php

namespace Tests\Feature;

use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecurringEventTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    private function make(array $extra, string $start = '2026-11-14'): Event
    {
        return Event::create([
            'slug' => 'mountain-days-2026-11', 'title' => ['ru' => 'Mountain Days'],
            'starts_on' => $start, 'extra_dates' => $extra, 'time_text' => '12:00–16:00', 'is_published' => true,
        ]);
    }

    public function test_extra_dates_are_normalised_and_end_on_is_last_date(): void
    {
        $e = $this->make(['2026-11-28', '2026-11-21', '2026-11-21', '2026-11-14']);
        $this->assertSame(['2026-11-21', '2026-11-28'], $e->extra_dates);
        $this->assertSame('2026-11-28', $e->ends_on->toDateString());
    }

    public function test_labels_for_recurring_event(): void
    {
        app()->setLocale('ru');
        $e = $this->make(['2026-11-21', '2026-11-28']);
        $this->assertSame('14, 21, 28 ноября', $e->dateLabel());

        $across = Event::create(['slug' => 'across', 'title' => ['ru' => 'X'], 'starts_on' => '2026-11-28', 'extra_dates' => ['2026-12-05']]);
        $this->assertSame('28 ноября, 5 декабря', $across->daysLabel());
    }

    public function test_stays_upcoming_until_last_date_and_shows_on_page(): void
    {
        $this->travelTo('2026-11-20 10:00');
        $e = $this->make(['2026-11-21', '2026-11-28']);
        $this->assertTrue(Event::upcoming()->whereKey($e->id)->exists());
        $this->assertSame('21', $e->dayBadge()); // ближайшая дата, а не прошедшая 14-е
        $this->get('/ru/events/'.$e->slug)->assertOk()->assertSee('14, 21, 28 ноября');
        $this->get('/ru/events')->assertOk()->assertSee('14, 21, 28 ноября');

        $this->travelTo('2026-11-29 10:00');
        $this->assertFalse(Event::upcoming()->whereKey($e->id)->exists());
    }

    public function test_single_day_event_unchanged(): void
    {
        $e = Event::create(['slug' => 'one-2026-11', 'title' => ['ru' => 'One'], 'starts_on' => '2026-11-14', 'is_published' => true]);
        $this->assertFalse($e->isRecurring());
        $this->assertNull($e->ends_on);
    }

    public function test_admin_uses_custom_calendar_not_browser_date_input(): void
    {
        $this->actingAs(\App\Models\User::factory()->create());
        $this->get('/admin/events/create')->assertOk()->assertDontSee('type="date"', false)->assertSee('Другие даты');
        $this->get('/admin/offers/create')->assertOk()->assertDontSee('type="date"', false);
        $this->get('/admin/stories/create')->assertOk()->assertDontSee('type="datetime-local"', false);
    }
}
