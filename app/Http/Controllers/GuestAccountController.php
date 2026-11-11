<?php

namespace App\Http\Controllers;

use App\Support\Locales;
use App\Support\Seo;

/**
 * Личный кабинет гостя: встроенный модуль Exely (TravelLine) «guest-account».
 * Бронирования и данные гостя живут в Exely, поэтому своего кабинета на сайте нет — сайт только даёт страницу в своём оформлении.
 * Как и виджет отзывов, модуль работает через прежний загрузчик (пространство имён bookingengine).
 */
class GuestAccountController extends Controller
{
    public function __invoke()
    {
        return view('site.guest-account', [
            'hotel' => null,
            'exelyLocale' => Locales::all()[app()->getLocale()]['exely'] ?? 'ru',
            'seo' => Seo::make(__('site.booking.account'))->noindex(),
        ]);
    }
}
