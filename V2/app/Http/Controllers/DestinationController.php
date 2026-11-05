<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\RoomType;
use App\Support\Seo;

/**
 * Страницы отеля. Подразделы как у карточки отеля Marriott / Aman:
 * обзор, номера, рестораны, впечатления, предложения, галерея, как добраться.
 */
class DestinationController extends Controller
{
    public function index()
    {
        return view('site.destinations.index', [
            'open' => Hotel::published()->open()->ordered()->get(),
            'comingSoon' => Hotel::published()->where('status', Hotel::STATUS_COMING_SOON)->ordered()->get(),
            'seo' => Seo::make(__('site.nav.destinations'), __('site.destinations.intro')),
        ]);
    }

    public function show(Hotel $hotel)
    {
        $this->visible($hotel);

        return view('site.destinations.show', [
            'hotel' => $hotel,
            'rooms' => $hotel->rooms()->published()->ordered()->get(),
            'venues' => $hotel->venues()->published()->ordered()->get(),
            'experiences' => $hotel->experiences()->published()->ordered()->limit(6)->get(),
            'offers' => $hotel->offers()->active()->ordered()->limit(3)->get(),
            'seo' => Seo::for($hotel)->withSchema(Seo::hotelSchema($hotel)),
        ]);
    }

    public function rooms(Hotel $hotel)
    {
        $this->visible($hotel);

        return view('site.destinations.rooms', [
            'hotel' => $hotel,
            'rooms' => $hotel->rooms()->published()->ordered()->get(),
            'seo' => Seo::make(__('site.hotel.rooms').' — '.$hotel->t('name'), $hotel->t('summary'), $hotel->hero_image),
        ]);
    }

    public function room(Hotel $hotel, RoomType $room)
    {
        $this->visible($hotel);
        abort_unless($room->is_published, 404);

        return view('site.destinations.room', [
            'hotel' => $hotel,
            'room' => $room,
            'others' => $hotel->rooms()->published()->whereKeyNot($room->id)->ordered()->get(),
            'seo' => Seo::for($room, $hotel->t('name')),
        ]);
    }

    public function dining(Hotel $hotel)
    {
        $this->visible($hotel);

        return view('site.destinations.dining', [
            'hotel' => $hotel,
            'venues' => $hotel->venues()->published()->ordered()->get(),
            'seo' => Seo::make(__('site.hotel.dining').' — '.$hotel->t('name'), $hotel->t('summary')),
        ]);
    }

    public function experiences(Hotel $hotel)
    {
        $this->visible($hotel);

        return view('site.destinations.experiences', [
            'hotel' => $hotel,
            'experiences' => $hotel->experiences()->published()->ordered()->get(),
            'seo' => Seo::make(__('site.hotel.experiences').' — '.$hotel->t('name'), $hotel->t('summary')),
        ]);
    }

    public function offers(Hotel $hotel)
    {
        $this->visible($hotel);

        return view('site.destinations.offers', [
            'hotel' => $hotel,
            'offers' => $hotel->offers()->active()->ordered()->get(),
            'seo' => Seo::make(__('site.hotel.offers').' — '.$hotel->t('name'), $hotel->t('summary')),
        ]);
    }

    public function gallery(Hotel $hotel)
    {
        $this->visible($hotel);

        return view('site.destinations.gallery', [
            'hotel' => $hotel,
            'seo' => Seo::make(__('site.hotel.gallery').' — '.$hotel->t('name'), $hotel->t('summary')),
        ]);
    }

    public function gettingHere(Hotel $hotel)
    {
        $this->visible($hotel);

        return view('site.destinations.getting-here', [
            'hotel' => $hotel,
            'faqs' => $hotel->faqs()->where('is_published', true)->orderBy('sort')->get(),
            'seo' => Seo::make(__('site.hotel.getting_here').' — '.$hotel->t('name'), $hotel->t('summary')),
        ]);
    }

    private function visible(Hotel $hotel): void
    {
        abort_unless($hotel->is_published, 404);
    }
}
