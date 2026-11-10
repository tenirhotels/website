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
        \Livewire\Livewire::test(\App\Filament\Widgets\QuickActions::class)->assertSee('Новое предложение');
    }
}
