<?php

namespace Database\Seeders;

use App\Models\Redirect;
use Illuminate\Database\Seeder;

/**
 * 301-редиректы:
 *  - V1 (Laravel 8): /hotel, /about, /activities, /news, /booking — язык был в сессии, ведём на /ru;
 *  - конструктор Exely (текущий tenirhotels.com): /ru-ru/…, /kk-kz/…, /en-gb/…, /zh-cn/….
 * Параметры запроса (?room-type=62361, ?special-offer=…) переносятся middleware автоматически.
 */
class RedirectsSeeder extends Seeder
{
    public function run(): void
    {
        // Сайт запускается в режиме одного отеля — цели в коротких адресах.
        // Когда включится режим сети, короткие адреса сами отдадут 301 на /destinations/{отель}/...
        $map = [
            // V1
            '/hotel' => '/ru',
            '/about' => '/ru/about',
            '/activities' => '/ru/experiences',
            '/news' => '/ru/stories',
            '/booking' => '/ru/booking',
        ];

        // Конструктор Exely
        $exely = [
            '' => '',
            'about' => 'about',
            'information' => 'before-you-arrive',
            'news' => 'stories',
            'photos' => 'gallery',
            'reviews' => '',
            'events' => 'experiences?category=celebrations',
            'restaurants' => 'dining',
            'rooms' => 'rooms',
            'special-offers' => 'offers',
            'services' => 'experiences',
            'conference-halls' => 'experiences',
            'excursions' => 'experiences',
            'contacts' => 'contact',
            'booking' => 'booking',
            'guest-account' => 'booking',
            'terms-of-use' => 'terms',
        ];
        foreach (['ru-ru' => 'ru', 'kk-kz' => 'kk', 'en-gb' => 'en', 'zh-cn' => 'zh'] as $old => $new) {
            foreach ($exely as $from => $to) {
                $map["/$old".($from ? "/$from" : '')] = "/$new".($to ? "/$to" : '');
            }
        }

        foreach ($map as $from => $to) {
            Redirect::updateOrCreate(['from_path' => Redirect::normalize($from)], ['to_path' => $to, 'status_code' => 301, 'is_active' => true]);
        }
    }
}
