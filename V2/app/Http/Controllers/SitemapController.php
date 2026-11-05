<?php

namespace App\Http\Controllers;

use App\Models\Experience;
use App\Models\Hotel;
use App\Models\Offer;
use App\Models\Page;
use App\Models\Story;
use App\Support\Locales;

/**
 * sitemap.xml: все языки, у каждого адреса — альтернативы hreflang.
 */
class SitemapController extends Controller
{
    public function __invoke()
    {
        $entries = []; // [[route, params, lastmod]]

        $entries[] = ['home', [], null];
        $entries[] = ['destinations.index', [], null];
        $entries[] = ['experiences.index', [], null];
        $entries[] = ['offers.index', [], null];
        $entries[] = ['stories.index', [], null];
        $entries[] = ['contact', [], null];

        foreach (Hotel::published()->with(['rooms' => fn ($q) => $q->published()])->get() as $hotel) {
            $entries[] = ['destinations.show', ['hotel' => $hotel->slug], $hotel->updated_at];
            if ($hotel->isOpen()) {
                foreach (['rooms', 'dining', 'experiences', 'offers', 'gallery', 'getting-here'] as $sub) {
                    $entries[] = ["destinations.$sub", ['hotel' => $hotel->slug], $hotel->updated_at];
                }
                foreach ($hotel->rooms as $room) {
                    $entries[] = ['destinations.rooms.show', ['hotel' => $hotel->slug, 'room' => $room->slug], $room->updated_at];
                }
            }
        }
        foreach (Experience::published()->get() as $e) {
            $entries[] = ['experiences.show', ['experience' => $e->slug], $e->updated_at];
        }
        foreach (Offer::active()->get() as $o) {
            $entries[] = ['offers.show', ['offer' => $o->slug], $o->updated_at];
        }
        foreach (Story::live()->get() as $s) {
            $entries[] = ['stories.show', ['story' => $s->slug], $s->updated_at];
        }
        foreach (Page::published()->where('placement', '!=', 'hidden')->get() as $p) {
            $entries[] = ['page', ['page' => $p->slug], $p->updated_at];
        }

        $urls = [];
        foreach ($entries as [$name, $params, $lastmod]) {
            $alternates = [];
            foreach (Locales::all() as $code => $meta) {
                $alternates[$meta['hreflang']] = route($name, [...$params, 'locale' => $code]);
            }
            foreach ($alternates as $href) {
                $urls[] = ['loc' => $href, 'lastmod' => $lastmod?->toAtomString(), 'alternates' => $alternates];
            }
        }

        return response()
            ->view('site.sitemap', ['urls' => $urls, 'default' => route('home', ['locale' => Locales::default()])])
            ->header('Content-Type', 'application/xml');
    }
}
