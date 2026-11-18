<?php

namespace Tests\Feature;

use App\Models\Redirect;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestAccountTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_guest_account_page_embeds_exely_module_in_all_languages(): void
    {
        foreach (['kk', 'ru', 'en', 'zh'] as $l) {
            $this->get("/$l/guest-account")->assertOk()
                ->assertSee("'guest-account'", false)->assertSee('tl-guest-account', false)
                ->assertSee('/booking', false)->assertSee('noindex', false);
        }
        $this->get('/ru/guest-account')->assertSee('Личный кабинет гостя');
    }

    public function test_old_exely_account_urls_redirect_and_robots_hides_it(): void
    {
        $this->get('/ru-ru/guest-account/')->assertRedirect('/ru/guest-account');
        $this->get('/en-gb/guest-account')->assertRedirect('/en/guest-account');
        $this->assertSame('/ru/guest-account', Redirect::where('from_path', '/ru-ru/guest-account')->value('to_path'));
        $this->get('/robots.txt')->assertSee('Disallow: /*/guest-account', false);
        $this->get('/ru/booking')->assertSee('/ru/guest-account', false);
    }
}
