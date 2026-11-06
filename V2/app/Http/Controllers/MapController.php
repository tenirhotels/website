<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Support\ResortMap;
use App\Support\Seo;

/** Интерактивная карта курорта (зима / лето): /{locale}/map. */
class MapController extends Controller
{
    public function __invoke()
    {
        $hotel = Hotel::published()->open()->ordered()->get()->firstWhere('slug', config('tenir.recognition_hotel'))
            ?? Hotel::published()->open()->ordered()->first();

        return view('site.map', [
            'hotel' => $hotel,
            'pins' => ResortMap::pins(),
            'season' => ResortMap::defaultSeason(),
            'seo' => Seo::make(__('map.title'), __('map.lead')),
        ]);
    }
}
