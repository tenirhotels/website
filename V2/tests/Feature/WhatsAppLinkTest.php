<?php

namespace Tests\Feature;

use Tests\TestCase;

class WhatsAppLinkTest extends TestCase
{
    public function test_wa_url_has_prefilled_text_per_locale(): void
    {
        foreach (['kk', 'ru', 'en', 'zh'] as $l) {
            app()->setLocale($l);
            $url = wa_url('+7 727 313 14 15', 'page', ['title' => 'Баня', 'hotel' => 'Tenir']);
            $this->assertStringStartsWith('https://wa.me/77273131415?text=', $url);
            $this->assertStringContainsString(rawurlencode('Баня'), $url);
            $this->assertStringNotContainsString('site.wa', urldecode($url));
        }
        $this->assertNull(wa_url(''));
    }

    public function test_email_wraps_before_at(): void
    {
        $this->assertSame('reservations<wbr>@tenirhotels.com', (string) email_wrap('reservations@tenirhotels.com'));
    }
}
