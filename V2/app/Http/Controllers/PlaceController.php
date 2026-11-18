<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Support\Property;
use App\Support\Seo;
use App\Support\Weather;

/** Страница локации: /{locale}/place/{slug} — о месте, погода, камеры, трассы, карта. */
class PlaceController extends Controller
{
    public function show(Location $location)
    {
        abort_unless($location->hasPlacePage(), 404);

        $hotels = $location->hotels()->published()->ordered()->get();
        $hotel = $hotels->first();
        $seo = Seo::make($location->t('name'), $location->t('lead') ?: strip_tags((string) $location->t('about')), $location->place_image ?: $hotel?->hero_image)
            ->withSchema(Seo::placeSchema($location));

        return view('site.place', [
            'location' => $location,
            'hotel' => Property::isSingle() ? Property::primary() : $hotel,
            'hotels' => $hotels,
            'seo' => $seo,
        ]);
    }

    /** JSON для блока погоды (вне кэша страниц): /api/weather/{slug}?alt=3200 */
    public function weather(Location $location, int $alt)
    {
        $allowed = collect($location->weatherPoints())->pluck('altitude_m')->all();
        abort_unless($location->hasPlacePage() && in_array($alt, $allowed, true), 404);

        $data = Weather::for($location->latitude, $location->longitude, $alt);

        return response()->json($data ?: ['error' => true], $data ? 200 : 503)
            ->header('Cache-Control', 'public, max-age=300');
    }
}
