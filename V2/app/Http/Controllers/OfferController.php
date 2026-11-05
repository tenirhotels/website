<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Support\Seo;

class OfferController extends Controller
{
    public function index()
    {
        return view('site.offers.index', [
            'offers' => Offer::active()->with('hotel')->ordered()->get(),
            'seo' => Seo::make(__('site.nav.offers'), __('site.offers.intro')),
        ]);
    }

    public function show(Offer $offer)
    {
        abort_unless($offer->is_published, 404);

        return view('site.offers.show', [
            'offer' => $offer,
            'expired' => $offer->valid_to && $offer->valid_to->isPast(),
            'others' => Offer::active()->whereKeyNot($offer->id)->ordered()->limit(3)->get(),
            'seo' => Seo::for($offer),
        ]);
    }
}
