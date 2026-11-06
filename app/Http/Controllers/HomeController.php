<?php

namespace App\Http\Controllers;

use App\Models\Experience;
use App\Models\Hotel;
use App\Models\Offer;
use App\Models\Page;
use App\Models\Story;
use App\Support\Property;
use App\Support\Seo;

/**
 * Главная.
 *  Режим одного отеля — главная и есть страница отеля (как у бутик-отелей).
 *  Режим сети — витрина бренда: отели, впечатления сети, предложения (как у Six Senses / Aman).
 */
class HomeController extends Controller
{
    public function __invoke()
    {
        $shared = [
            'brandPage' => Page::published()->where('slug', 'about')->first(),
            'sustainabilityPage' => Page::published()->where('slug', 'sustainability')->first(),
            'stories' => Story::live()->latest('published_at')->limit(3)->get(),
        ];

        if (Property::isSingle()) {
            $hotel = Property::primary();
            $seo = Seo::make()->withSchema(Seo::organizationSchema());
            if ($hotel) {
                $seo->withSchema(Seo::hotelSchema($hotel));
                $seo->image = $hotel->hero_image;
            }

            return view('site.home.single', [
                ...$shared,
                'hotel' => $hotel,
                'rooms' => $hotel?->rooms()->published()->ordered()->get() ?? collect(),
                'venues' => $hotel?->venues()->published()->ordered()->get() ?? collect(),
                'experiences' => Experience::published()->where('is_featured', true)->ordered()->limit(3)->get(),
                'offers' => Offer::active()->where('is_featured', true)->ordered()->limit(3)->get(),
                'seo' => $seo,
            ]);
        }

        return view('site.home.multi', [
            ...$shared,
            'hotels' => Hotel::published()->open()->ordered()->get(),
            'comingSoon' => Hotel::published()->where('status', Hotel::STATUS_COMING_SOON)->ordered()->get(),
            'experiences' => Experience::published()->with('hotel')->where('is_featured', true)->ordered()->limit(3)->get(),
            'offers' => Offer::active()->with('hotel')->where('is_featured', true)->ordered()->limit(3)->get(),
            'seo' => Seo::make()->withSchema(Seo::organizationSchema()),
        ]);
    }
}
