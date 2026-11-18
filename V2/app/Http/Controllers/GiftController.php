<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Support\Gifts;
use App\Support\Seo;

/** Подарочные сертификаты: список из gifter, заказ и оплата — на его странице. */
class GiftController extends Controller
{
    public function __invoke()
    {
        abort_unless(Gifts::enabled(), 404);

        return view('site.gifts.index', [
            'gifts' => Gifts::offers(),
            'hotel' => Hotel::published()->open()->ordered()->first(),
            'seo' => Seo::make(__('gifts.title'), __('gifts.lead')),
        ]);
    }
}
