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
}
