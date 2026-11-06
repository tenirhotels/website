<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\Offer;
use App\Support\Property;
use Illuminate\Http\Request;
use App\Support\Seo;

class OfferController extends Controller
{
    public function index(Request $request)
    {
        $hotel = Property::isMulti() ? Hotel::published()->where('slug', $request->query('hotel'))->first() : null;

        return view('site.offers.index', [
            'offers' => Offer::active()->with('hotel')
                ->when($hotel, fn ($q) => $q->where(fn ($q) => $q->where('hotel_id', $hotel->id)->orWhereNull('hotel_id')))
                ->ordered()->get(),
            'hotels' => Property::isMulti() ? Hotel::published()->open()->ordered()->get() : collect(),
            'activeHotel' => $hotel,
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
