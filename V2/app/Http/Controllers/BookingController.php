<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Support\Locales;
use App\Support\Seo;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

/**
 * Страница бронирования с модулем TravelLine / Exely (booking-form).
 *
 * Плашка бронирования отправляет «понятные» параметры:
 *   ?hotel=shymbulak&check_in=2026-12-12&check_out=2026-12-14&adults=2&children=0&promo_code=...
 * Контроллер переводит их в параметры модуля (config/tenir.php → exely.params)
 * и перенаправляет на тот же адрес — модуль читает их из адреса страницы.
 * Старые ссылки Exely (?room-type=62361, ?special-offer=…) работают как есть.
 */
class BookingController extends Controller
{
    /**
     * Параметры плашки, которых нет в адресе модуля Exely. adults и children сюда не входят:
     * у Exely они называются так же, и иначе адрес после перенаправления снова считался бы «плашкой»
     * (бесконечный цикл редиректов).
     */
    private const FRIENDLY = ['check_in', 'check_out', 'promo_code', 'room_type', 'special_offer'];

    public function __invoke(Request $request)
    {
        if ($request->hasAny(self::FRIENDLY)) {
            return redirect()->route('booking', $this->toExely($request));
        }

        $hotels = Hotel::published()->open()->ordered()->get();
        $hotel = $hotels->firstWhere('slug', $request->query('hotel')) ?? $hotels->first();

        return view('site.booking', [
            'hotel' => $hotel,
            'context' => $hotel?->exelyContext() ?? config('tenir.exely.context'),
            'exelyLocale' => Locales::all()[app()->getLocale()]['exely'] ?? 'ru',
            'seo' => Seo::make(__('site.booking.title'))->noindex(),
        ]);
    }

    private function toExely(Request $request): array
    {
        $map = config('tenir.exely.params');
        $query = array_filter(['hotel' => $request->query('hotel')]);

        $checkIn = $this->date($request->query('check_in'));
        $checkOut = $this->date($request->query('check_out'));

        if ($checkIn && $checkIn->isBefore(CarbonImmutable::today())) {
            $checkIn = CarbonImmutable::today();
        }
        if ($checkIn) {
            $query[$map['check_in']] = $checkIn->toDateString();
            $nights = $checkOut && $checkOut->greaterThan($checkIn) ? (int) $checkIn->diffInDays($checkOut) : 1;
            $query[$map['nights']] = min($nights, 30);
        }

        $adults = (int) $request->query('adults', 0);
        if ($adults > 0) {
            $query[$map['adults']] = min($adults, config('tenir.exely.max_adults'));
        }

        // Дети: количество ("2") или возраст через запятую ("5,9") — формат уточнить у Exely.
        $children = (string) $request->query('children', '');
        if (preg_match('/^\d{1,2}(,\d{1,2})*$/', $children) && $children !== '0') {
            $query[$map['children']] = $children;
        }

        foreach (['room_type', 'special_offer', 'promo_code'] as $key) {
            $value = trim((string) $request->query($key, ''));
            if ($value !== '' && preg_match('/^[\w-]{1,64}$/u', $value)) {
                $query[$map[$key]] = $value;
            }
        }

        return $query;
    }

    private function date(?string $value): ?CarbonImmutable
    {
        if (! $value || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return null;
        }

        try {
            return CarbonImmutable::createFromFormat('Y-m-d', $value)->startOfDay();
        } catch (\Throwable) {
            return null;
        }
    }
}
