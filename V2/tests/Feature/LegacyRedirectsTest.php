<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LegacyRedirectsTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_old_events_and_reviews_pages_go_to_new_pages(): void
    {
        foreach (['ru-ru' => 'ru', 'kk-kz' => 'kk', 'en-gb' => 'en', 'zh-cn' => 'zh'] as $old => $new) {
            $this->get("/$old/events/")->assertStatus(301)->assertRedirect("/$new/events");
            $this->get("/$old/reviews/")->assertStatus(301)->assertRedirect("/$new/reviews");
            $this->get("/$new/events")->assertOk();
            $this->get("/$new/reviews")->assertOk();
        }
    }
}
