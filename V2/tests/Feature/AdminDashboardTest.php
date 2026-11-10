<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_dashboard_and_menu_pages_render(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        foreach (['/admin', '/admin/offers', '/admin/events', '/admin/faqs', '/admin/site-settings'] as $url) {
            $this->get($url)->assertOk();
        }
        $this->get('/admin')->assertSee('Обзор сайта')->assertSee('Открыть сайт');

        // Виджеты подгружаются лениво — проверяем их отдельно.
        \Livewire\Livewire::test(\App\Filament\Widgets\SiteHealth::class)->assertSee('внимания');
        \Livewire\Livewire::test(\App\Filament\Widgets\SiteStats::class)->assertSee('Сейчас на сайте');
        \Livewire\Livewire::test(\App\Filament\Widgets\QuickActions::class)->assertSee('Создать')->assertSee('Предложение');
    }

    public function test_location_map_form_saves_without_losing_data(): void
    {
        $this->actingAs(User::factory()->create());
        $loc = \App\Models\Location::where('slug', 'shymbulak')->first();
        $pins = count($loc->map['pins']);

        \Livewire\Livewire::test(\App\Filament\Resources\Locations\Pages\EditLocation::class, ['record' => $loc->getKey()])
            ->fillForm(['map.one_way_minutes' => 70])
            ->call('save')
            ->assertHasNoFormErrors();

        $map = $loc->fresh()->map;
        $this->assertSame(70, (int) $map['one_way_minutes']);
        $this->assertCount($pins, $map['pins']);
        $this->assertCount(4, $map['stations']);
        $this->assertSame('Станция Шымбулак', $map['stations'][1]['name']['ru']);
        $this->assertSame(3, count($map['geo_points']));
    }
}
