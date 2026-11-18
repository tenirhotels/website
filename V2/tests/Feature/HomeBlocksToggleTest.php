<?php

namespace Tests\Feature;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeBlocksToggleTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_home_blocks_can_be_switched_off(): void
    {
        $this->get('/ru')->assertSee(__('site.home.offers_title', [], 'ru'))->assertSee(__('site.home.experiences_title', [], 'ru'));

        Setting::put('home', ['experiences' => false, 'offers' => false, 'events' => false]);

        $this->get('/ru')->assertOk()
            ->assertDontSee(__('site.home.offers_title', [], 'ru'))
            ->assertDontSee(__('site.home.experiences_title', [], 'ru'))
            ->assertDontSee(__('site.home.events_title', [], 'ru'));
    }

    public function test_recognition_switch(): void
    {
        \App\Models\Recognition::query()->update(['is_published' => true, 'show_on_home' => true]);
        if (\App\Models\Recognition::forHome()->isEmpty()) {
            $this->markTestSkipped('Нет подтверждённых наград в сидах.');
        }
        $this->get('/ru')->assertSee('recognition-title', false);
        Setting::put('home', ['recognition' => false]);
        $this->get('/ru')->assertDontSee('recognition-title', false);
    }

    public function test_settings_form_shows_defaults_and_saves_gifts(): void
    {
        $this->actingAs(\App\Models\User::factory()->create());

        \Livewire\Livewire::test(\App\Filament\Pages\SiteSettings::class)
            ->assertFormSet(['home.experiences' => true, 'home.offers' => true, 'home.events' => true, 'home.recognition' => true, 'gifts.on_home' => false, 'gifts.enabled' => true])
            ->fillForm(['gifts.on_home' => true, 'gifts.url' => 'https://gifts.example.test', 'home.offers' => false])
            ->call('save');

        $this->assertTrue(Setting::get('gifts.on_home'));
        $this->assertSame('https://gifts.example.test', Setting::get('gifts.url'));
        $this->assertFalse(Setting::get('home.offers'));

        \Livewire\Livewire::test(\App\Filament\Pages\SiteSettings::class)
            ->assertFormSet(['home.offers' => false, 'home.events' => true, 'gifts.on_home' => true]);
    }
}
