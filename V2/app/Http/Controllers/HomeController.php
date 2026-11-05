<?php

namespace App\Http\Controllers;

use App\Models\Experience;
use App\Models\Hotel;
use App\Models\Offer;
use App\Models\Page;
use App\Models\Story;
use App\Support\Seo;

/**
 * Главная. Пока открыт один отель — главная рассказывает о нём (как у бутик-брендов);
 * когда отелей станет больше, блок «Отели и курорты» показывает витрину всех направлений
 * (как «In the spotlight» у Six Senses).
 */
class HomeController extends Controller
{
    public function __invoke()
    {
        $hotels = Hotel::published()->open()->ordered()->get();
        $featured = $hotels->firstWhere('is_featured', true) ?? $hotels->first();

        return view('site.home', [
            'hotels' => $hotels,
            'featured' => $featured,
            'rooms' => $featured?->rooms()->published()->ordered()->get() ?? collect(),
            'comingSoon' => Hotel::published()->where('status', Hotel::STATUS_COMING_SOON)->ordered()->get(),
            'experiences' => Experience::published()->where('is_featured', true)->ordered()->limit(6)->get(),
            'offers' => Offer::active()->where('is_featured', true)->ordered()->limit(3)->get(),
            'brandPage' => Page::published()->where('slug', 'about')->first(),
            'sustainabilityPage' => Page::published()->where('slug', 'sustainability')->first(),
            'stories' => Story::live()->latest('published_at')->limit(3)->get(),
            'seo' => Seo::make()->withSchema(Seo::organizationSchema()),
        ]);
    }
}
