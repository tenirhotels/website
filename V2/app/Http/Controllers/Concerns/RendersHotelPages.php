<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Hotel;
use App\Models\RoomType;
use App\Support\Seo;

/** Рендер страниц отеля — общий для режима сети и режима одного отеля. */
trait RendersHotelPages
{
    protected function renderOverview(Hotel $hotel)
    {
        return view('site.destinations.show', [
            'hotel' => $hotel,
            'rooms' => $hotel->rooms()->published()->ordered()->get(),
            'venues' => $hotel->venues()->published()->ordered()->get(),
            'experiences' => $hotel->experiences()->published()->ordered()->limit(6)->get(),
            'offers' => $hotel->offers()->active()->ordered()->limit(3)->get(),
            'seo' => Seo::for($hotel)->withSchema(Seo::hotelSchema($hotel)),
        ]);
    }

    protected function renderRooms(Hotel $hotel)
    {
        return view('site.destinations.rooms', [
            'hotel' => $hotel,
            'rooms' => $hotel->rooms()->published()->ordered()->get(),
            'seo' => Seo::make($this->title('rooms', $hotel), $hotel->t('summary'), $hotel->hero_image)->withKeywords($hotel->seoKeywords()),
        ]);
    }

    protected function renderRoom(Hotel $hotel, RoomType $room)
    {
        abort_unless($room->is_published, 404);

        return view('site.destinations.room', [
            'hotel' => $hotel,
            'room' => $room,
            'others' => $hotel->rooms()->published()->whereKeyNot($room->id)->ordered()->get(),
            'seo' => Seo::for($room, $hotel->t('name'))->withSchema(Seo::roomSchema($room, $hotel)),
        ]);
    }

    protected function renderDining(Hotel $hotel)
    {
        return view('site.destinations.dining', [
            'hotel' => $hotel,
            'venues' => $hotel->venues()->published()->ordered()->get(),
            'seo' => Seo::make($this->title('dining', $hotel), $hotel->t('summary'))->withKeywords($hotel->seoKeywords()),
        ]);
    }

    protected function renderExperiences(Hotel $hotel)
    {
        return view('site.destinations.experiences', [
            'hotel' => $hotel,
            'experiences' => $hotel->experiences()->published()->ordered()->get(),
            'seo' => Seo::make($this->title('experiences', $hotel), $hotel->t('summary'))->withKeywords($hotel->seoKeywords()),
        ]);
    }

    protected function renderOffers(Hotel $hotel)
    {
        return view('site.destinations.offers', [
            'hotel' => $hotel,
            'offers' => $hotel->offers()->active()->ordered()->get(),
            'seo' => Seo::make($this->title('offers', $hotel), $hotel->t('summary'))->withKeywords($hotel->seoKeywords()),
        ]);
    }

    protected function renderGallery(Hotel $hotel)
    {
        return view('site.destinations.gallery', [
            'hotel' => $hotel,
            'seo' => Seo::make($this->title('gallery', $hotel), $hotel->t('summary'))->withKeywords($hotel->seoKeywords()),
        ]);
    }

    protected function renderGettingHere(Hotel $hotel)
    {
        return view('site.destinations.getting-here', [
            'hotel' => $hotel,
            'faqs' => $hotel->faqs()->where('is_published', true)->orderBy('sort')->get(),
            'seo' => Seo::make($this->title('getting_here', $hotel), $hotel->t('summary'))->withKeywords($hotel->seoKeywords()),
        ]);
    }

    private function title(string $key, Hotel $hotel): string
    {
        return __("site.hotel.$key").' — '.$hotel->t('name');
    }
}
