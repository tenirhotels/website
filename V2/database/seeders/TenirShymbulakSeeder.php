<?php

namespace Database\Seeders;

use App\Models\Experience;
use App\Models\Faq;
use App\Models\Hotel;
use App\Models\Offer;
use App\Models\RoomType;
use App\Models\Venue;
use Database\Seeders\Concerns\CopiesMedia;
use Illuminate\Database\Seeder;

/**
 * Tenir Shymbulak — контент с текущего сайта tenirhotels.com (ru/en).
 * Казахский и китайский пока берутся по цепочке fallback — заполните в админке.
 * [В квадратных скобках] — то, что нужно уточнить.
 */
class TenirShymbulakSeeder extends Seeder
{
    use CopiesMedia;

    public function run(): void
    {
        $hotel = Hotel::updateOrCreate(['slug' => 'shymbulak'], [
            'name' => ['ru' => 'Tenir Shymbulak', 'en' => 'Tenir Shymbulak', 'kk' => 'Tenir Shymbulak', 'zh' => 'Tenir Shymbulak'],
            'tagline' => [
                'ru' => 'Тишина гор, звёзды и снег — у вас за окном',
                'en' => 'Mountain silence, stars and snow outside your window',
                'kk' => 'Таулардың тыныштығы, жұлдыздар мен қар — терезеңіздің сыртында',
            ],
            'summary' => [
                'ru' => 'Самый высокогорный эко-отель Казахстана: десять отдельных модулей на высоте 3 200 м среди ледников Заилийского Алатау.',
                'en' => 'The highest-altitude eco hotel in Kazakhstan: ten private modules at 3,200 m among the glaciers of the Trans-Ili Alatau.',
            ],
            'description' => [
                'ru' => '<p>Tenir — уникальный эко-отель на высоте 3 200 метров над уровнем моря в окружении горных хребтов Заилийского Алатау. Он создан для тех, кто хочет жить в горной тишине и наслаждаться нетронутой природой, оставаясь в комфортных номерах в одной из лучших эко-локаций Центральной Азии.</p><p>Это настоящий ski-in / ski-out отель вдали от городского шума. Гости живут в отдельных модулях со всеми современными удобствами. В отеле всего десять номеров, и в каждом есть всё для полного отдыха и комфорта.</p>',
                'en' => '<p>Tenir is a unique eco hotel at 3,200 metres above sea level, surrounded by the ranges of the Trans-Ili Alatau. It is made for those who wish to live in mountain silence and enjoy pristine nature while staying in comfortable rooms in one of the finest eco locations in Central Asia.</p><p>A true ski-in / ski-out hotel far from the noise of the city. Guests stay in detached modules with all modern amenities. There are only ten rooms, each with everything needed for complete rest and comfort.</p>',
            ],
            'status' => Hotel::STATUS_OPEN,
            'region' => ['ru' => 'Заилийский Алатау', 'en' => 'Trans-Ili Alatau', 'kk' => 'Іле Алатауы'],
            'city' => ['ru' => 'Алматы', 'en' => 'Almaty', 'kk' => 'Алматы'],
            'address' => [
                'ru' => 'Высота 3 200 м, курорт Шымбулак, Алматы, Казахстан',
                'en' => 'Altitude 3,200 m, Shymbulak Mountain Resort, Almaty, Kazakhstan',
            ],
            'country_code' => 'KZ',
            'altitude_m' => 3200,
            'latitude' => 43.113991,
            'longitude' => 77.111328,
            'phone' => '+7 727 313 14 15',
            'whatsapp' => '+7 727 313 14 15',
            'email' => 'reservations@tenirhotels.com',
            'hero_image' => $this->media('tenir-modules.webp'),
            'gallery' => array_values(array_filter([
                $this->media('tenir-reception.webp'),
                $this->media('tenir-modules.webp'),
                $this->media('tenir-stilts.webp'),
                $this->media('tenir-glacier.webp'),
                $this->media('tenir-logo.webp'),
                $this->media('hotel-1.webp'),
            ])),
            'highlights' => [
                ['value' => '3 200 м', 'label' => ['ru' => 'над уровнем моря', 'en' => 'above sea level', 'kk' => 'теңіз деңгейінен']],
                ['value' => '10', 'label' => ['ru' => 'отдельных модулей', 'en' => 'private modules', 'kk' => 'жеке модуль']],
                ['value' => 'Ski-in', 'label' => ['ru' => 'прямо к трассам', 'en' => 'straight to the slopes', 'kk' => 'трассаларға тікелей']],
            ],
            'getting_here' => [
                'ru' => '<p>Отель находится на высоте 3 200 м на курорте Шымбулак. Добраться можно по канатной дороге от Медеу через Шымбулак до верхних станций.</p><p>По запросу мы встретим вас на Шымбулаке и поможем с багажом до отеля (услуга «Шымбулак ⇌ Tenir»). Также доступен трансфер от Шымбулака до центра Алматы.</p><p>[Добавьте расписание канатной дороги, время в пути и рекомендации по одежде.]</p>',
                'en' => '<p>The hotel is located at 3,200 m at Shymbulak Mountain Resort, reached by cable car from Medeu via Shymbulak to the upper stations.</p><p>On request we meet you at Shymbulak and help with your luggage to the hotel (Shymbulak ⇌ Tenir service). A transfer from Shymbulak to Almaty city centre is also available.</p><p>[Add the cable car timetable, travel time and clothing tips.]</p>',
            ],
            'is_featured' => true,
            'is_published' => true,
            'sort' => 1,
        ]);

        RoomType::updateOrCreate(['hotel_id' => $hotel->id, 'slug' => 'deluxe-mountain-view'], [
            'name' => ['ru' => 'Deluxe с видом на горы', 'en' => 'Deluxe Room with Mountain View', 'kk' => 'Таулы көрінісі бар Deluxe'],
            'summary' => [
                'ru' => 'Панорамное окно во всю стену на хребты Заилийского Алатау и собственная финская сауна.',
                'en' => 'A floor-to-ceiling panoramic window onto the Trans-Ili Alatau and a private Finnish sauna.',
            ],
            'description' => [
                'ru' => '<p>Стильные комфортабельные двухместные модульные дома с возможностью размещения дополнительной кровати для детей с 8 лет. Из номера открывается великолепный вид на горы Заилийского Алатау.</p>',
                'en' => '<p>Stylish, comfortable two-person modular houses with room for an extra bed for children from 8 years old. The room opens onto magnificent views of the Trans-Ili Alatau mountains.</p>',
            ],
            'view' => ['ru' => 'Панорама на горы', 'en' => 'Mountain panorama'],
            'bed' => ['ru' => 'King-size', 'en' => 'King-size'],
            'max_guests' => 2,
            'amenities' => [
                'ru' => \Database\Seeders\Concerns\RoomContent::amenities('ru'),
                'en' => \Database\Seeders\Concerns\RoomContent::amenities('en'),
            ],
            'image' => $this->media('tenir-stilts.webp'),
            'gallery' => array_values(array_filter([$this->media('tenir-modules.webp'), $this->media('tenir-glacier.webp')])),
            'exely_room_type_id' => '62361',
            'is_published' => true,
            'sort' => 1,
        ]);

        Venue::updateOrCreate(['hotel_id' => $hotel->id, 'slug' => 'restaurant'], [
            'type' => 'restaurant',
            'name' => ['ru' => 'Ресторан Tenir', 'en' => 'Tenir Restaurant', 'kk' => 'Tenir мейрамханасы'],
            'summary' => [
                'ru' => 'Авторская кухня на высоте 3 200 м: завтраки и ужины по специальному меню рядом с модулями.',
                'en' => 'Signature cuisine at 3,200 m: breakfasts and dinners from a dedicated menu, right next to the modules.',
            ],
            'description' => [
                'ru' => '<p>[Опишите кухню, шефа, терраса с видом на горы, вино.]</p>',
                'en' => '<p>[Describe the cuisine, the chef, the terrace with mountain views, wine.]</p>',
            ],
            'hours' => ['ru' => 'Завтрак для гостей отеля 08:30–11:30', 'en' => 'Breakfast for hotel guests 08:30–11:30'],
            'image' => $this->media('hotel-1.webp'),
            'is_published' => true,
            'sort' => 1,
        ]);

        $offers = [
            ['breakfast-dinner', '10137078', 'offer-1.webp', null,
                ['ru' => 'Завтрак и ужин', 'en' => 'Breakfast and dinner', 'kk' => 'Таңғы ас пен кешкі ас'],
                ['ru' => 'Проживание с завтраком и ужином в ресторане отеля по специальному меню.', 'en' => 'Accommodation with breakfast and dinner at the hotel restaurant from a specially crafted menu.']],
            ['romantic-stay', '10018244', 'about.webp', null,
                ['ru' => 'Романтическое пребывание', 'en' => 'Romantic stay', 'kk' => 'Романтикалық демалыс'],
                ['ru' => 'Номер оформлен в стиле «Романтические моменты», бутылка вина в комплимент.', 'en' => 'The room is decorated in the “Romantic moments” style, with a complimentary bottle of wine.']],
            ['long-stay', '295761', 'hero-back.webp', 3,
                ['ru' => 'Длительное проживание', 'en' => 'Long stay', 'kk' => 'Ұзақ демалыс'],
                ['ru' => 'Скидка 10% от базового тарифа при проживании от трёх ночей.', 'en' => '10% off the base rate for stays of three nights or more.']],
        ];
        foreach ($offers as $i => [$slug, $exely, $img, $nights, $title, $summary]) {
            Offer::updateOrCreate(['slug' => $slug], [
                'hotel_id' => $hotel->id,
                'title' => $title,
                'summary' => $summary,
                'description' => ['ru' => '<p>'.$summary['ru'].'</p>', 'en' => '<p>'.$summary['en'].'</p>'],
                'exely_special_offer_id' => $exely,
                'min_nights' => $nights,
                'image' => $this->media($img),
                'is_featured' => true,
                'is_published' => true,
                'sort' => $i + 1,
            ]);
        }

        $experiences = [
            ['tenir-sky', 'sky', ['winter', 'spring', 'summer', 'autumn'], 'exp-6.webp',
                ['ru' => 'Tenir Sky — ближе всех к звёздам', 'en' => 'Tenir Sky — closest to the stars', 'kk' => 'Tenir Sky — жұлдыздарға ең жақын'],
                ['ru' => 'Календарь «Сегодня в Tenir»: что видно невооружённым глазом с террасы ресторана.', 'en' => '“Tonight at Tenir”: what you can see with the naked eye from the restaurant terrace.'], null],
            ['hiking', 'active', ['summer', 'autumn'], 'exp-2.webp',
                ['ru' => 'Хайкинг-пасс Tenir', 'en' => 'Tenir hiking pass', 'kk' => 'Tenir хайкинг-пасы'],
                ['ru' => 'Тропы к ледникам и вершинам Заилийского Алатау прямо от отеля.', 'en' => 'Trails to the glaciers and peaks of the Trans-Ili Alatau, straight from the hotel.'], null],
            ['horse-riding', 'active', ['summer', 'autumn'], 'exp-3.webp',
                ['ru' => 'Конная прогулка', 'en' => 'Horse riding', 'kk' => 'Атпен серуен'],
                ['ru' => 'Часовая прогулка верхом по горным склонам.', 'en' => 'An hour on horseback across the mountain slopes.'], ['ru' => '60 минут', 'en' => '60 minutes', 'kk' => '60 минут']],
            ['ski-in-ski-out', 'active', ['winter', 'spring'], 'room-1.webp',
                ['ru' => 'Лыжи от порога', 'en' => 'Skiing from your door', 'kk' => 'Табалдырықтан шаңғы'],
                ['ru' => 'Ski-in / ski-out, Multipass и помощь с багажом от канатной дороги.', 'en' => 'Ski-in / ski-out, Multipass and luggage assistance from the cable car.'], null],
            ['welcome-tour', 'culture', ['winter', 'spring', 'summer', 'autumn'], 'exp-4.webp',
                ['ru' => 'Велком-тур', 'en' => 'Welcome tour', 'kk' => 'Велком-тур'],
                ['ru' => 'Знакомство с отелем, горами и историей имени Тәңір.', 'en' => 'An introduction to the hotel, the mountains and the story behind the name Tengri.'], null],
            ['photo-session', 'celebrations', ['winter', 'spring', 'summer', 'autumn'], 'exp-5.webp',
                ['ru' => 'Фотосессия на высоте 3 200 м', 'en' => 'Photo session at 3,200 m', 'kk' => '3 200 м биіктікте фотосессия'],
                ['ru' => 'Пакет без проживания: съёмка среди ледников и облаков.', 'en' => 'A package without accommodation: a shoot among the glaciers and clouds.'], null],
        ];
        foreach ($experiences as $i => [$slug, $category, $seasons, $img, $title, $summary, $duration]) {
            Experience::updateOrCreate(['slug' => $slug], [
                'hotel_id' => $hotel->id,
                'category' => $category,
                'seasons' => $seasons,
                'title' => $title,
                'summary' => $summary,
                'description' => ['ru' => '<p>'.$summary['ru'].' [Дополните описание.]</p>', 'en' => '<p>'.$summary['en'].' [Add details.]</p>'],
                'duration' => $duration,
                'image' => $this->media($img),
                'is_featured' => $i < 3,
                'is_published' => true,
                'sort' => $i + 1,
            ]);
        }

        $faqs = [
            [['ru' => 'Как добраться до отеля?', 'en' => 'How do I get to the hotel?'],
             ['ru' => '<p>По канатной дороге от Медеу через Шымбулак. По запросу мы встречаем гостей и помогаем с багажом.</p>', 'en' => '<p>By cable car from Medeu via Shymbulak. On request we meet guests and help with luggage.</p>']],
            [['ru' => 'Есть ли скидка при длительном проживании?', 'en' => 'Is there a discount for longer stays?'],
             ['ru' => '<p>Да, при проживании от трёх ночей — скидка 10% от базового тарифа.</p>', 'en' => '<p>Yes, 10% off the base rate for stays of three nights or more.</p>']],
        ];
        foreach ($faqs as $i => [$q, $a]) {
            Faq::updateOrCreate(['hotel_id' => $hotel->id, 'sort' => $i + 1], ['question' => $q, 'answer' => $a, 'is_published' => true]);
        }
    }
}
