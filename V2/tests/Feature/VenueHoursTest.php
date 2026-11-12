<?php

namespace Tests\Feature;

use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VenueHoursTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_dining_page_shows_break_and_breakfast_hours(): void
    {
        $v = Venue::query()->firstOrFail();
        $v->update([
            'hours' => ['ru' => '08:30–21:30'],
            'break_hours' => ['ru' => '15:00–17:00'],
            'breakfast_hours' => ['ru' => '08:30–11:30'],
        ]);

        $this->get('/ru/dining')->assertOk()
            ->assertSee('08:30–21:30')->assertSee('Перерыв')->assertSee('15:00–17:00')
            ->assertSee('Завтрак для гостей отеля')->assertSee('08:30–11:30');
    }
}
