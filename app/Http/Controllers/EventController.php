<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Hotel;
use App\Support\Property;
use App\Support\Seo;
use Illuminate\Http\Request;

/** Афиша: что планируется на курорте и в отелях. */
class EventController extends Controller
{
    public function index(Request $request)
    {
        $hotel = Property::isMulti() ? Hotel::published()->where('slug', $request->query('hotel'))->first() : null;

        $events = Event::upcoming()->with('hotel')
            ->when($hotel, fn ($q) => $q->where(fn ($q) => $q->where('hotel_id', $hotel->id)->orWhereNull('hotel_id')))
            ->get();

        return view('site.events.index', [
            'months' => $events->groupBy(function (Event $e) {
                $d = $e->isRecurring() ? $e->nextDay() : $e->starts_on;

                return $d->isPast() ? now()->format('Y-m') : $d->format('Y-m');
            }),
            'hotels' => Property::isMulti() ? Hotel::published()->open()->ordered()->get() : collect(),
            'activeHotel' => $hotel,
            'seo' => Seo::make(__('site.nav.events'), __('site.events.intro')),
        ]);
    }

    public function show(Event $event)
    {
        abort_unless($event->is_published, 404);

        return view('site.events.show', [
            'event' => $event,
            'others' => Event::upcoming()->whereKeyNot($event->id)->limit(3)->get(),
            'seo' => Seo::for($event, $event->hotel?->t('name'))->withSchema(Seo::eventSchema($event)),
        ]);
    }
}
