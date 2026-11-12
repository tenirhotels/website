<?php

namespace Tests\Feature;

use App\Models\Hotel;
use App\Models\Offer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Единый формат заголовков: «Страница — Отель» (один отель) и с брендом сети в режиме сети. */
class PageTitlesTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    private function title(string $url): string
    {
        preg_match('~<title>(.*?)</title>~s', $this->get($url)->assertOk()->getContent(), $m);

        return html_entity_decode(trim($m[1]));
    }

    public function test_single_hotel_titles_name_only_the_hotel(): void
    {
        $hotel = Hotel::where('slug', 'shymbulak')->first();
        $name = $hotel->t('name');
        $offer = Offer::published()->first();

        foreach (['/ru/rooms', '/ru/offers', '/ru/events', '/ru/contact', '/ru/offers/'.$offer->slug, '/ru'] as $url) {
            $t = $this->title($url);
            $this->assertStringContainsString($name, $t, $url);
            $this->assertStringNotContainsString('| Tenir Hotels', $t, $url);
        }
        $this->assertSame('Предложения — '.$name, $this->title('/ru/offers'));
        $this->assertStringContainsString('og:site_name" content="'.e($name), $this->get('/ru/offers')->getContent());
    }

    public function test_single_hotel_hero_has_no_repeated_hotel_name(): void
    {
        $name = Hotel::where('slug', 'shymbulak')->first()->t('name');
        $html = $this->get('/ru/rooms')->getContent();
        $this->assertDoesNotMatchRegularExpression('~<p class="eyebrow-light">\s*'.preg_quote(e($name), '~').'\s*</p>~', $html);
    }

    public function test_multi_mode_keeps_brand_suffix(): void
    {
        Hotel::create([
            'slug' => 'almaty', 'name' => ['ru' => 'Tenir Almaty'], 'status' => Hotel::STATUS_OPEN,
            'is_published' => true, 'sort' => 2,
        ]);
        $this->assertSame('Предложения | Tenir Hotels', $this->title('/ru/offers'));
        $this->assertStringContainsString('| Tenir Hotels', $this->title('/ru/destinations/shymbulak/rooms'));
    }
}
