<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Support\Property;

/**
 * Короткие адреса режима «один отель»: /{locale}/rooms, /rooms/{room}, /dining, /gallery, /getting-here.
 * Обзор отеля — главная (HomeController), впечатления и предложения — общие /experiences и /offers.
 * В режиме сети эти адреса отдают 301 на страницы главного отеля: /destinations/{отель}/...
 */
class SingleHotelController extends Controller
{
    use Concerns\RendersHotelPages;

    public function rooms()
    {
        return $this->toFull('rooms') ?? $this->renderRooms($this->hotel());
    }

    public function room(string $room)
    {
        return $this->toFull('room', ['room' => $room])
            ?? $this->renderRoom($this->hotel(), $this->hotel()->rooms()->where('slug', $room)->firstOrFail());
    }

    public function dining()
    {
        return $this->toFull('dining') ?? $this->renderDining($this->hotel());
    }

    public function gallery()
    {
        return $this->toFull('gallery') ?? $this->renderGallery($this->hotel());
    }

    public function gettingHere()
    {
        return $this->toFull('getting-here') ?? $this->renderGettingHere($this->hotel());
    }

    public function reviews()
    {
        return $this->toFull('reviews') ?? $this->renderReviews($this->hotel());
    }

    private function hotel(): Hotel
    {
        return Property::primary() ?? abort(404);
    }

    /** Режим сети: короткий адрес → страница главного отеля (301). */
    private function toFull(string $section, array $params = [])
    {
        if (Property::isSingle()) {
            return null;
        }

        return redirect()->to(
            route(Property::routeName($section, Property::MULTI), [...$params, 'hotel' => $this->hotel()->slug]),
            301,
        );
    }
}
