<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OfferPageTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_offer_card_shows_facts_in_every_language(): void
    {
        foreach (['kk' => 'Ұсыныс шарттары', 'ru' => 'Условия предложения', 'en' => 'Offer at a glance', 'zh' => '优惠概览'] as $lang => $heading) {
            $offer = \App\Models\Offer::active()->whereNotNull('min_nights')->first();
            if (! $offer) { $this->markTestSkipped('no offer with min_nights in seed'); }
            $this->get('/'.$lang.'/offers/'.$offer->slug)->assertOk()->assertSee($heading);
        }
    }
}
