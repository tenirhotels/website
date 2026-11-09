<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\RoomType;
use App\Support\Property;
use App\Support\Seo;

/**
 * Страницы отеля в режиме сети: /{locale}/destinations/{hotel}/...
 * Подразделы как у карточки отеля Marriott / Aman.
 * В режиме одного отеля эти адреса отдают 301 на короткие (/rooms, /dining, ...).
 * Сами страницы рендерятся трейтом RendersHotelPages — его же использует SingleHotelController.
 */
class DestinationController extends Controller
{
    use Concerns\RendersHotelPages;

    public function index()
    {
        if (Property::isSingle()) {
            return redirect()->route('home', status: 301);
        }

        return view('site.destinations.index', [
            'open' => Hotel::published()->open()->ordered()->get(),
            'comingSoon' => Hotel::published()->where('status', Hotel::STATUS_COMING_SOON)->ordered()->get(),
            'seo' => Seo::make(__('site.nav.destinations'), __('site.destinations.intro')),
        ]);
    }

    public function show(Hotel $hotel)
    {
        return $this->toShort($hotel, 'overview') ?? $this->renderOverview($hotel);
    }

    public function rooms(Hotel $hotel)
    {
        return $this->toShort($hotel, 'rooms') ?? $this->renderRooms($hotel);
    }

    public function room(Hotel $hotel, RoomType $room)
    {
        return $this->toShort($hotel, 'room', ['room' => $room->slug]) ?? $this->renderRoom($hotel, $room);
    }

    public function dining(Hotel $hotel)
    {
        return $this->toShort($hotel, 'dining') ?? $this->renderDining($hotel);
    }

    public function experiences(Hotel $hotel)
    {
        return $this->toShort($hotel, 'experiences') ?? $this->renderExperiences($hotel);
    }

    public function offers(Hotel $hotel)
    {
        return $this->toShort($hotel, 'offers') ?? $this->renderOffers($hotel);
    }

    public function gallery(Hotel $hotel)
    {
        return $this->toShort($hotel, 'gallery') ?? $this->renderGallery($hotel);
    }

    public function gettingHere(Hotel $hotel)
    {
        return $this->toShort($hotel, 'getting-here') ?? $this->renderGettingHere($hotel);
    }

    public function reviews(Hotel $hotel)
    {
        return $this->toShort($hotel, 'reviews') ?? $this->renderReviews($hotel);
    }

    /** Режим одного отеля: длинный адрес → короткий (301). */
    private function toShort(Hotel $hotel, string $section, array $params = [])
    {
        abort_unless($hotel->is_published, 404);

        return Property::isSingleHotel($hotel)
            ? redirect()->to($hotel->sectionUrl($section, $params), 301)
            : null;
    }
}
