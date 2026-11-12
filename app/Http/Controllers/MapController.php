<?php

namespace App\Http\Controllers;

use App\Support\Property;
use App\Support\ResortMap;
use App\Support\Seo;

/** Интерактивная карта курорта (зима / лето): /{locale}/map. Карта — часть локации отеля. */
class MapController extends Controller
{
    public function __invoke()
    {
        $hotel = Property::mapHotel();
        abort_unless($hotel, 404);

        return view('site.map', [
            'hotel' => $hotel,
            'map' => $hotel->resortMap(),
            'season' => ResortMap::defaultSeason(),
            'seo' => Seo::make(__('map.title'), __('map.lead')),
        ]);
    }
}
