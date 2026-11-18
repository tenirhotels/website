<?php

use App\Models\Experience;
use App\Models\Faq;
use App\Models\Hotel;
use App\Models\Offer;
use App\Models\Setting;
use App\Models\Venue;
use Illuminate\Database\Migrations\Migration;

/**
 * Данные с текущего сайта tenirhotels.com (страницы «Информация», «Услуги», «Номера», «Ресторан»,
 * «Мероприятия», «Спецпредложения», «Контакты»): канатные дороги, багаж, Multipass, условия
 * проживания, часы работы. Заполняются только пустые поля и черновые заглушки вида «[Дополните…]»;
 * то, что уже отредактировано в админке, не перезаписывается.
 */
return new class extends Migration
{
    /** Пусто или заглушка — можно заменить. */
    private function replaceable(?string $current, array $seeds = []): bool
    {
        $current = trim((string) $current);

        return $current === '' || str_contains($current, '[') || in_array($current, $seeds, true);
    }

    /** Заполняет языки переводимого поля, не трогая правки редакторов. */
    private function fill($model, string $attr, array $values, array $seedsByLocale = []): void
    {
        $current = (array) ($model->getAttribute($attr) ?? []);
        foreach ($values as $locale => $text) {
            if ($this->replaceable($current[$locale] ?? null, $seedsByLocale[$locale] ?? [])) {
                $current[$locale] = $text;
            }
        }
        $model->setAttribute($attr, $current);
    }

    private function terms(): array
    {
        return [
            'ru' => '<h3>Условия проживания</h3><ul><li>Заезд с 15:30, выезд до 11:00.</li><li>Для гарантии бронирования требуется полная предоплата.</li><li>Депозит 50 000 ₸ вносится на стойке регистрации.</li><li>Дети от 6 до 14 лет включительно: доплата 30 000 ₸ за ночь, включая завтрак и отдельное спальное место. Заселение детей младше 6 лет на высоте 3 200 м не разрешено.</li><li>Отмена в летний сезон (20.03–31.10): без штрафа за 5 суток до заезда, позже — штраф в размере стоимости первой ночи.</li><li>Отмена в зимний сезон (01.11–19.03): без штрафа за 14 суток до заезда, позже — штраф 100% за весь период.</li><li>При изменении длительности или дат проживания тариф может измениться.</li></ul>',
            'en' => '<h3>Terms of stay</h3><ul><li>Check-in from 15:30, check-out by 11:00.</li><li>Full prepayment is required to guarantee the booking.</li><li>A deposit of 50,000 ₸ is paid at the front desk.</li><li>Children aged 6 to 14 inclusive: a surcharge of 30,000 ₸ per night, including breakfast and a separate bed. Children under 6 cannot be accommodated at 3,200 m.</li><li>Summer season (20 Mar – 31 Oct): free cancellation up to 5 days before arrival; later, a penalty equal to the first night.</li><li>Winter season (1 Nov – 19 Mar): free cancellation up to 14 days before arrival; later, a 100% penalty for the whole stay.</li><li>If the length of stay or the dates change, the rate may change.</li></ul>',
            'kk' => '<h3>Тұру шарттары</h3><ul><li>Кіру 15:30-дан, шығу 11:00-ге дейін.</li><li>Брондауды кепілдендіру үшін толық алдын ала төлем қажет.</li><li>50 000 ₸ депозит ресепшнде төленеді.</li><li>6 жастан 14 жасқа дейінгі (қоса алғанда) балалар: түніне 30 000 ₸ үстеме, таңғы ас пен жеке төсек орны қоса. 6 жасқа дейінгі балаларды 3 200 м биіктікте орналастыруға болмайды.</li><li>Жазғы маусымда (20.03–31.10) бас тарту: келуге дейін 5 тәулік қалғанда айыппұлсыз, кейін — бірінші түннің құнындай айыппұл.</li><li>Қысқы маусымда (01.11–19.03) бас тарту: келуге дейін 14 тәулік қалғанда айыппұлсыз, кейін — бүкіл кезең үшін 100% айыппұл.</li><li>Тұру мерзімі немесе күндері өзгерсе, тариф өзгеруі мүмкін.</li></ul>',
        ];
    }

    public function up(): void
    {
        $hotel = Hotel::where('slug', 'shymbulak')->first();
        if (! $hotel) {
            return; // чистая установка: данные придут из сидеров
        }

        // --- Отель: заезд/выезд, как добраться ---
        $hotel->check_in_time = $hotel->check_in_time ?: '15:30';
        $hotel->check_out_time = $hotel->check_out_time ?: '11:00';

        $route = [
            'ru' => '<p>Отель Tenir находится на высоте 3 200 м на курорте Шымбулак. Добраться до него можно только по канатной дороге курорта.</p>'
                .'<h3>Маршрут</h3><ol>'
                .'<li><strong>Нижняя станция «Медеу»</strong> — 21–22 км от центра Алматы, 30–40 минут на автомобиле.</li>'
                .'<li><strong>Гондола Медеу — Шымбулак</strong> — около 4,5 км, 22–24 минуты.</li>'
                .'<li><strong>Комби-1</strong>: Шымбулак — средняя станция (2 800 м), 12–13 минут.</li>'
                .'<li><strong>Комби-2</strong>: средняя станция — Tenir (3 200 м), 7–8 минут.</li></ol>'
                .'<p>От центра города до отеля в среднем около 1,5 часов. В выходные и праздничные дни на станциях возможны очереди — путь может занять больше времени.</p>'
                .'<h3>Что важно знать</h3><ul>'
                .'<li>Проживание не даёт автоматического безлимитного доступа к канатной дороге. Для гостей есть разовый пеший билет Tenir (Медеу — Tenir и обратно) и многодневный Multipass на 3, 5 или 7 дней: свободное передвижение по Комби-1 и Комби-2, до четырёх подъёмов в день.</li>'
                .'<li>Электронный билет приходит в день заезда на контакты из брони. Билеты, купленные в другом месте на следующий день, не действуют.</li>'
                .'<li>Последняя посадка — за 30 минут до закрытия канатной дороги. График и вечерний доступ определяет курорт Шымбулак. Пешеходный билет не даёт права катания.</li>'
                .'<li>Багаж поднимается по канатной дороге, дальше — короткие пешие переходы: берите лёгкий багаж. Помощь портье (от станции «Шымбулак», начало Комби-1, до номера) — за дополнительную плату, заказывайте заранее.</li>'
                .'<li>На высоте прохладнее: одежда по погоде.</li></ul>',
            'en' => '<p>Tenir is at 3,200 m at Shymbulak Mountain Resort and can only be reached by the resort’s cable cars.</p>'
                .'<h3>The route</h3><ol>'
                .'<li><strong>Medeu lower station</strong> — 21–22 km from central Almaty, 30–40 minutes by car.</li>'
                .'<li><strong>Medeu – Shymbulak gondola</strong> — about 4.5 km, 22–24 minutes.</li>'
                .'<li><strong>Combi-1</strong>: Shymbulak to the mid-station (2,800 m), 12–13 minutes.</li>'
                .'<li><strong>Combi-2</strong>: mid-station to Tenir (3,200 m), 7–8 minutes.</li></ol>'
                .'<p>From the city centre to the hotel takes about 1.5 hours on average. At weekends and on public holidays there may be queues at the stations, so the journey can take longer.</p>'
                .'<h3>Good to know</h3><ul>'
                .'<li>A stay does not include unlimited cable car access. Guests can use a single Tenir pedestrian ticket (Medeu – Tenir and back) or a multi-day Multipass for 3, 5 or 7 days: free travel on Combi-1 and Combi-2, up to four ascents a day.</li>'
                .'<li>The e-ticket is sent on the day of arrival to the contacts in your booking. Tickets bought elsewhere do not work the next day.</li>'
                .'<li>Last boarding is 30 minutes before the cable car closes. Schedules and evening access are set by Shymbulak resort. A pedestrian ticket does not allow skiing.</li>'
                .'<li>Luggage goes up by cable car, followed by short walks, so pack light. Porter assistance (from Shymbulak station, the start of Combi-1, to your room) is available for an extra fee — please book in advance.</li>'
                .'<li>It is cooler at altitude: dress for the weather.</li></ul>',
            'kk' => '<p>Tenir қонақүйі Шымбұлақ курортында 3 200 м биіктікте орналасқан. Оған тек курорттың аспалы жолымен жетуге болады.</p>'
                .'<h3>Бағыт</h3><ol>'
                .'<li><strong>«Медеу» төменгі станциясы</strong> — Алматы орталығынан 21–22 км, көлікпен 30–40 минут.</li>'
                .'<li><strong>Медеу — Шымбұлақ гондоласы</strong> — шамамен 4,5 км, 22–24 минут.</li>'
                .'<li><strong>Комби-1</strong>: Шымбұлақ — аралық станция (2 800 м), 12–13 минут.</li>'
                .'<li><strong>Комби-2</strong>: аралық станция — Tenir (3 200 м), 7–8 минут.</li></ol>'
                .'<p>Қала орталығынан қонақүйге дейін орта есеппен шамамен 1,5 сағат. Демалыс және мерекелік күндері станцияларда кезек болуы мүмкін, жол ұзағырақ болады.</p>'
                .'<h3>Білу маңызды</h3><ul>'
                .'<li>Тұру аспалы жолды шектеусіз пайдалануды қамтымайды. Қонақтарға Tenir жаяу жүргіншілер билеті (Медеу — Tenir және кері) немесе 3, 5, 7 күндік Multipass бар: Комби-1 және Комби-2 бойынша еркін жүру, күніне төрт көтерілуге дейін.</li>'
                .'<li>Электронды билет келген күні брондағы байланыс деректеріне жіберіледі. Басқа жерден сатып алынған билеттер келесі күні жарамайды.</li>'
                .'<li>Соңғы отырғызу — аспалы жол жабылуынан 30 минут бұрын. Кесте мен кешкі қолжетімділікті Шымбұлақ курорты белгілейді. Жаяу жүргіншілер билеті шаңғы тебуге құқық бермейді.</li>'
                .'<li>Жүк аспалы жолмен көтеріледі, одан әрі қысқа жаяу өтпелер бар, сондықтан жеңіл жүк алыңыз. Портье көмегі («Шымбұлақ» станциясынан, Комби-1 басынан, нөмірге дейін) қосымша ақыға, алдын ала тапсырыс беріңіз.</li>'
                .'<li>Биіктікте салқынырақ: ауа райына сай киініңіз.</li></ul>',
        ];
        $current = (array) $hotel->getting_here;
        foreach ($route as $locale => $html) {
            $text = (string) ($current[$locale] ?? '');
            // Заменяем короткий черновик из сидера; длинные правки редакторов не трогаем.
            if (mb_strlen(strip_tags($text)) < 400 || str_contains($text, '[')) {
                $current[$locale] = $html;
            }
        }
        $hotel->getting_here = $current;
        $hotel->save();

        // --- Контакты для корпоративных запросов ---
        $contacts = Setting::get('contacts', []);
        $contacts['sales_phone'] ??= '+7 701 427 18 48';
        $contacts['sales_email'] ??= 'sales@tenirhotels.com';
        Setting::put('contacts', $contacts);

        // --- Предложения ---
        $terms = $this->terms();
        $offerText = [
            'breakfast-dinner' => [
                'ru' => '<p>Проживание с завтраком и ужином в ресторане отеля по специальному меню.</p><h3>Что включено</h3><ul><li>Завтрак и ужин в ресторане Tenir на высоте 3 200 м</li><li>Финская сауна в номере</li><li>Телевизор со стриминговым сервисом</li><li>Вода Shymbulak и чайный набор</li><li>Wi-Fi</li></ul>',
                'en' => '<p>A stay with breakfast and dinner at the hotel restaurant from a specially crafted menu.</p><h3>Included</h3><ul><li>Breakfast and dinner at Tenir restaurant, 3,200 m</li><li>Private Finnish sauna</li><li>TV with a streaming service</li><li>Shymbulak water and a tea set</li><li>Wi-Fi</li></ul>',
                'kk' => '<p>Қонақүй мейрамханасында арнайы мәзір бойынша таңғы және кешкі асы бар тұру.</p><h3>Не кіреді</h3><ul><li>3 200 м биіктіктегі Tenir мейрамханасында таңғы және кешкі ас</li><li>Нөмірдегі фин саунасы</li><li>Стриминг қызметі бар теледидар</li><li>Shymbulak суы және шай жиынтығы</li><li>Wi-Fi</li></ul>',
            ],
            'romantic-stay' => [
                'ru' => '<p>Номер оформлен в стиле «Romantic moments»: бутылка вина с ассорти сыров и сладкий комплимент.</p><h3>Что включено</h3><ul><li>Оформление номера «Romantic moments»</li><li>Бутылка вина, ассорти сыров, сладкий комплимент</li><li>Завтрак в ресторане на 3 200 м</li><li>Разрешение на фото- и видеосъёмку</li><li>Билеты на канатную дорогу в обе стороны</li><li>Финская сауна, телевизор, вода, чайный набор, Wi-Fi</li></ul>',
                'en' => '<p>The room is decorated in the “Romantic moments” style, with a bottle of wine, a cheese platter and a sweet treat.</p><h3>Included</h3><ul><li>“Romantic moments” room decoration</li><li>A bottle of wine, a cheese platter and a sweet treat</li><li>Breakfast at the restaurant at 3,200 m</li><li>Permission for photo and video shooting</li><li>Cable car tickets both ways</li><li>Finnish sauna, TV, water, tea set, Wi-Fi</li></ul>',
                'kk' => '<p>Нөмір «Romantic moments» стилінде безендірілген: бір бөтелке шарап, ірімшік ассортиі және тәттi сыйлық.</p><h3>Не кіреді</h3><ul><li>«Romantic moments» нөмір безендіруі</li><li>Шарап, ірімшік ассортиі, тәтті сыйлық</li><li>3 200 м биіктіктегі мейрамханада таңғы ас</li><li>Фото және бейне түсіруге рұқсат</li><li>Аспалы жолға екі жаққа билеттер</li><li>Фин саунасы, теледидар, су, шай жиынтығы, Wi-Fi</li></ul>',
            ],
            'long-stay' => [
                'ru' => '<p>Скидка 10% от основного тарифа при проживании более 3 ночей.</p><h3>Что включено</h3><ul><li>Завтрак в ресторане на 3 200 м</li><li>Финская сауна в номере</li><li>Телевизор, вода, чайный набор, Wi-Fi</li></ul>',
                'en' => '<p>10% off the base rate for stays of more than 3 nights.</p><h3>Included</h3><ul><li>Breakfast at the restaurant at 3,200 m</li><li>Private Finnish sauna</li><li>TV, water, tea set, Wi-Fi</li></ul>',
                'kk' => '<p>3 түннен көп тұрғанда негізгі тарифтен 10% жеңілдік.</p><h3>Не кіреді</h3><ul><li>3 200 м биіктіктегі мейрамханада таңғы ас</li><li>Нөмірдегі фин саунасы</li><li>Теледидар, су, шай жиынтығы, Wi-Fi</li></ul>',
            ],
        ];
        foreach ($offerText as $slug => $texts) {
            $offer = Offer::where('slug', $slug)->first();
            if (! $offer) {
                continue;
            }
            $desc = (array) $offer->description;
            foreach ($texts as $locale => $html) {
                $plain = trim(strip_tags((string) ($desc[$locale] ?? '')));
                $summary = trim((string) (((array) $offer->summary)[$locale] ?? ''));
                if ($plain === '' || $plain === $summary || str_contains((string) ($desc[$locale] ?? ''), '[')) {
                    $desc[$locale] = $html.$terms[$locale];
                }
            }
            $offer->description = $desc;
            if ($slug === 'long-stay' && (int) $offer->min_nights === 3) {
                $offer->min_nights = 4; // «более 3 ночей» на текущем сайте
                $summary = (array) $offer->summary;
                $summary['ru'] = 'Скидка 10% от основного тарифа при проживании более трёх ночей.';
                $summary['en'] = '10% off the base rate for stays of more than three nights.';
                $summary['kk'] = '3 түннен көп тұрғанда негізгі тарифтен 10% жеңілдік.';
                $offer->summary = $summary;
            }
            $offer->save();
        }

        // --- Впечатления ---
        $exp = [
            'tenir-sky' => ['ru' => '<p>Календарь «Сегодня в Tenir»: что видно невооружённым глазом с террасы ресторана — планеты, созвездия, Млечный Путь на высоте 3 200 м, вдали от городской засветки.</p>', 'en' => '<p>“Tonight at Tenir”: what you can see with the naked eye from the restaurant terrace — planets, constellations and the Milky Way at 3,200 m, far from city lights.</p>', 'kk' => '<p>«Бүгін Tenir-де» күнтізбесі: мейрамхана террасасынан қарапайым көзбен не көрінеді — планеталар, шоқжұлдыздар және қала жарығынан алыс 3 200 м биіктіктегі Құс жолы.</p>'],
            'hiking' => ['ru' => '<p>Пешие маршруты прямо от отеля: тропы к ледникам и вершинам Заилийского Алатау, трекинг, поездки на канатной дороге с видом на горы. Лето и осень — лучшее время для хайкинга; в сезон доступны также гид-туры и прогулки к ледникам. Подробности и подбор маршрута — у супервайзера отеля.</p>', 'en' => '<p>Walking routes straight from the hotel: trails to the glaciers and peaks of the Trans-Ili Alatau, trekking and scenic cable car rides. Summer and autumn are the best seasons for hiking; guided tours and glacier walks are also available in season. Ask the hotel supervisor for details and route advice.</p>', 'kk' => '<p>Қонақүйден тікелей жаяу маршруттар: Іле Алатауының мұздықтары мен шыңдарына апаратын соқпақтар, трекинг және таулы көрінісі бар аспалы жолмен серуен. Жаз бен күз — хайкингке ең қолайлы кезең; маусымда гид-турлар мен мұздықтарға серуендер де бар. Толығырақ мәліметті қонақүй супервайзерінен алыңыз.</p>'],
            'horse-riding' => ['ru' => '<p>Часовая прогулка верхом по горным склонам. Ежедневно с 10:00 до 17:30, по предварительной записи по телефонам +7 (700) 232 27 95 и +7 (777) 174 46 13. Услугу предоставляет ГК «Шымбулак».</p>', 'en' => '<p>An hour on horseback across the mountain slopes. Daily from 10:00 to 17:30, by prior booking on +7 (700) 232 27 95 and +7 (777) 174 46 13. The service is provided by Shymbulak resort.</p>', 'kk' => '<p>Тау беткейлерімен бір сағаттық атпен серуен. Күн сайын 10:00–17:30, +7 (700) 232 27 95 және +7 (777) 174 46 13 телефондары бойынша алдын ала жазылумен. Қызметті «Шымбұлақ» ГК көрсетеді.</p>'],
            'ski-in-ski-out' => ['ru' => '<p>Катание на горных лыжах и сноуборде с выходом из отеля на трассы курорта. Прокат снаряжения по уровню подготовки, частные инструкторы, помощь супервайзера с покупкой ски-пасса без очереди. Multipass для гостей отеля (3, 5 или 7 дней) — свободное передвижение по Комби-1 и Комби-2, до четырёх подъёмов в день; помощь портье с багажом от станции «Шымбулак». В номерах — безопасное хранение спортивного инвентаря.</p>', 'en' => '<p>Alpine skiing and snowboarding straight from the hotel to the resort’s slopes. Equipment rental to match your level, private instructors, and help from the hotel supervisor buying a ski pass without queuing. A Multipass for hotel guests (3, 5 or 7 days) gives free travel on Combi-1 and Combi-2, up to four ascents a day; porters help with luggage from Shymbulak station. Secure storage for sports equipment is available.</p>', 'kk' => '<p>Қонақүйден курорт трассаларына тікелей шығып, тау шаңғысы мен сноуборд тебу. Дайындық деңгейіне сай жабдық жалға алу, жеке нұсқаушылар, супервайзердің кезексіз ски-пас сатып алуға көмегі. Қонақтарға арналған Multipass (3, 5 немесе 7 күн) — Комби-1 және Комби-2 бойынша еркін жүру, күніне төрт көтерілуге дейін; «Шымбұлақ» станциясынан портье көмегі. Спорттық жабдықты қауіпсіз сақтау бар.</p>'],
            'welcome-tour' => ['ru' => '<p>Авторский тур с сертифицированными гидами: пешие прогулки и поездка на канатной дороге, знакомство с отелем, горами и историей имени Тәңір.</p><ul><li>Длительность около 2–3 часов, группа от 1 до 5 человек по одной брони.</li><li>Бронирование не позже чем за 2 дня; начало не раньше 10:00 и не позже 14:00.</li><li>Билеты на канатную дорогу не включены.</li><li>При опоздании более 30 минут тур отменяется без возврата оплаты.</li></ul>', 'en' => '<p>A signature tour with certified guides: walks and a cable car ride, an introduction to the hotel, the mountains and the story behind the name Tengri.</p><ul><li>About 2–3 hours, a group of 1 to 5 people per booking.</li><li>Book at least 2 days in advance; start between 10:00 and 14:00.</li><li>Cable car tickets are not included.</li><li>If you are more than 30 minutes late, the tour is cancelled without a refund.</li></ul>', 'kk' => '<p>Сертификатталған гидтермен авторлық тур: жаяу серуен және аспалы жолмен сапар, қонақүймен, таулармен және Тәңір атауының тарихымен танысу.</p><ul><li>Ұзақтығы шамамен 2–3 сағат, бір брондауға 1–5 адамнан топ.</li><li>Кемінде 2 күн бұрын брондау; басталуы 10:00-ден кеш емес және 14:00-ден кеш емес.</li><li>Аспалы жол билеттері кірмейді.</li><li>30 минуттан артық кешіксеңіз, тур төлемді қайтармай жойылады.</li></ul>'],
            'photo-session' => ['ru' => '<p>Пакет без проживания: съёмка среди ледников и облаков.</p><h3>Что входит</h3><ul><li>Фотосессия в Deluxe-номере с видом на горы с 12:00 до 14:00</li><li>Разрешение на фото- и видеосъёмку</li><li>4 билета на канатную дорогу</li><li>Ваучер на кофе и круассан в ресторане Tenir на 10 000 ₸</li><li>Бесплатная парковка для одного автомобиля на открытой зоне станции «Медеу»</li></ul>', 'en' => '<p>A package without accommodation: a shoot among the glaciers and clouds.</p><h3>Includes</h3><ul><li>A photo session in a Deluxe room with a mountain view, 12:00–14:00</li><li>Permission for photo and video shooting</li><li>4 cable car tickets</li><li>A 10,000 ₸ voucher for coffee and a croissant at Tenir restaurant</li><li>Free parking for one car in the open area at Medeu station</li></ul>', 'kk' => '<p>Тұрусыз пакет: мұздықтар мен бұлттар арасында түсірілім.</p><h3>Не кіреді</h3><ul><li>Таулы көрінісі бар Deluxe нөмірінде 12:00–14:00 фотосессия</li><li>Фото және бейне түсіруге рұқсат</li><li>Аспалы жолға 4 билет</li><li>Tenir мейрамханасында кофе мен круассанға 10 000 ₸ ваучер</li><li>«Медеу» станциясының ашық аймағында бір автокөлікке тегін тұрақ</li></ul>'],
        ];
        foreach ($exp as $slug => $texts) {
            if ($e = Experience::where('slug', $slug)->first()) {
                $this->fill($e, 'description', $texts);
                $e->save();
            }
        }

        $new = [
            ['bbq-party', 'celebrations', ['winter', 'spring'], 'exp-5.webp',
                ['ru' => 'BBQ-вечеринка TENIR', 'en' => 'TENIR BBQ party', 'kk' => 'TENIR BBQ кеші'],
                ['ru' => 'Самый высокогорный BBQ-обед: après-ski каждую субботу с 12:30 до 16:30.', 'en' => 'The highest BBQ lunch there is: après-ski every Saturday, 12:30–16:30.', 'kk' => 'Ең биік BBQ түскі асы: әр сенбі 12:30–16:30 après-ski.'],
                ['ru' => '<p>Гриль, стейки, хот-доги, глинтвейн и DJ — каждую субботу с 12:30 до 16:30. Самый высокогорный BBQ-обед — только в Tenir.</p>', 'en' => '<p>Grill, steaks, hot dogs, mulled wine and a DJ — every Saturday from 12:30 to 16:30. The highest BBQ lunch there is, only at Tenir.</p>', 'kk' => '<p>Гриль, стейктер, хот-догтар, глинтвейн және DJ — әр сенбі 12:30-дан 16:30-ға дейін. Ең биік BBQ түскі асы — тек Tenir-де.</p>'],
                ['ru' => 'Каждую субботу 12:30–16:30', 'en' => 'Every Saturday 12:30–16:30', 'kk' => 'Әр сенбі 12:30–16:30']],
            ['dinner-for-two', 'dining', ['winter', 'spring', 'summer', 'autumn'], 'hotel-1.webp',
                ['ru' => 'Ужин для двоих', 'en' => 'Dinner for two', 'kk' => 'Екеуіне арналған кешкі ас'],
                ['ru' => 'Заказное меню в ресторане Tenir на высоте 3 200 м — 60 000 ₸ за двоих.', 'en' => 'A set menu at Tenir restaurant at 3,200 m — 60,000 ₸ for two.', 'kk' => '3 200 м биіктіктегі Tenir мейрамханасында тапсырыс мәзірі — екеуіне 60 000 ₸.'],
                ['ru' => '<p>Ассорти брускетт; для неё и для него — закуска, основное блюдо, десерт (чизкейк или медовик), чай или кофе. Напитки: вода Acqua Panna или San Pellegrino 0,2 л и бутылка сухого белого или красного вина.</p><p>Стоимость — 60 000 ₸ за двоих. Бронирование за 24 часа.</p>', 'en' => '<p>An assortment of bruschetta; for her and for him — a starter, a main course, a dessert (cheesecake or honey cake), tea or coffee. Drinks: Acqua Panna or San Pellegrino water, 0.2 l, and a bottle of dry white or red wine.</p><p>60,000 ₸ for two. Book 24 hours in advance.</p>', 'kk' => '<p>Брускеттер ассорти; ол үшін және ол үшін — тіскебасар, негізгі тағам, десерт (чизкейк немесе медовик), шай немесе кофе. Сусындар: Acqua Panna немесе San Pellegrino суы 0,2 л және бір бөтелке құрғақ ақ немесе қызыл шарап.</p><p>Екеуіне 60 000 ₸. 24 сағат бұрын брондау.</p>'],
                null],
            ['events', 'celebrations', ['winter', 'spring', 'summer', 'autumn'], 'story-1.webp',
                ['ru' => 'Мероприятия и конференции', 'en' => 'Events and conferences', 'kk' => 'Іс-шаралар мен конференциялар'],
                ['ru' => 'Ивент-пространство на высоте 3 200 м: Купол, приватный зал PDR и две панорамные террасы.', 'en' => 'An event space at 3,200 m: the Dome, the private dining room (PDR) and two panoramic terraces.', 'kk' => '3 200 м биіктіктегі іс-шара кеңістігі: Күмбез, PDR жеке залы және екі панорамалық терраса.'],
                ['ru' => '<h3>Площадки</h3><ul><li><strong>Купол</strong> — главное пространство с панорамным обзором гор: презентации, стратегические сессии, торжественные ужины.</li><li><strong>PDR (Private Dining Room)</strong> — приватный зал для переговоров, камерных встреч и закрытых праздников.</li><li><strong>Две открытые террасы</strong> — кофе-брейки, welcome-зоны и неформальное общение.</li></ul><h3>Форматы</h3><ul><li>Презентации и деловые встречи, мини-конференции на 10–20 человек; проектор и экраны.</li><li>Камерные тимбилдинги: гастротимбилдинг с шеф-поваром, мастер-классы по коктейлям, творческие воркшопы, wellness-сессии и mindful walks.</li><li>Семейные и частные праздники: дни рождения, private dinner, дегустации, интерактивные игры и квесты.</li></ul><p>Гибкая рассадка, кофе-брейки, индивидуальное гастрономическое сопровождение и персональная координация команды отеля. Обеды и ужины для групп — по предварительному заказу.</p>', 'en' => '<h3>Spaces</h3><ul><li><strong>The Dome</strong> — the main space with panoramic mountain views: presentations, strategy sessions, gala dinners.</li><li><strong>PDR (Private Dining Room)</strong> — a private room for negotiations, intimate meetings and private celebrations.</li><li><strong>Two open terraces</strong> — coffee breaks, welcome areas and informal networking.</li></ul><h3>Formats</h3><ul><li>Presentations and business meetings, mini-conferences for 10–20 people; projector and screens.</li><li>Intimate team building: gastro team building with the chef, cocktail classes, creative workshops, wellness sessions and mindful walks.</li><li>Family and private celebrations: birthdays, private dinners, tastings, interactive games and quests.</li></ul><p>Flexible seating, coffee breaks, tailored dining and personal coordination by the hotel team. Group lunches and dinners by prior order.</p>', 'kk' => '<h3>Алаңдар</h3><ul><li><strong>Күмбез</strong> — таулардың панорамалық көрінісі бар басты кеңістік: презентациялар, стратегиялық сессиялар, салтанатты кешкі астар.</li><li><strong>PDR (Private Dining Room)</strong> — келіссөздерге, жабық кездесулер мен мерекелерге арналған жеке зал.</li><li><strong>Екі ашық терраса</strong> — кофе-брейктер, welcome-аймақтар және еркін қарым-қатынас.</li></ul><h3>Форматтар</h3><ul><li>Презентациялар мен іскерлік кездесулер, 10–20 адамға арналған шағын конференциялар; проектор мен экрандар.</li><li>Жабық тимбилдингтер: аспазбен гастротимбилдинг, коктейль шеберлік сабақтары, шығармашылық воркшоптар, wellness-сессиялар және mindful walks.</li><li>Отбасылық және жеке мерекелер: туған күндер, private dinner, дегустациялар, интерактивті ойындар мен квесттер.</li></ul><p>Икемді отырғызу, кофе-брейктер, жеке гастрономиялық сүйемелдеу және қонақүй командасының жеке үйлестіруі. Топтарға түскі және кешкі астар — алдын ала тапсырыспен.</p>'],
                null],
        ];
        $sort = (int) Experience::where('hotel_id', $hotel->id)->max('sort');
        foreach ($new as [$slug, $category, $seasons, $img, $title, $summary, $description, $duration]) {
            if (Experience::where('slug', $slug)->exists()) {
                continue;
            }
            Experience::create([
                'hotel_id' => $hotel->id, 'slug' => $slug, 'category' => $category, 'seasons' => $seasons,
                'title' => $title, 'summary' => $summary, 'description' => $description, 'duration' => $duration,
                'image' => Experience::where('slug', 'photo-session')->value('image'), // фото заменить в админке
                'is_featured' => false, 'is_published' => true, 'sort' => ++$sort,
            ]);
        }

        // --- Ресторан ---
        if ($venue = Venue::where('hotel_id', $hotel->id)->where('slug', 'restaurant')->first()) {
            $this->fill($venue, 'description', [
                'ru' => '<p>Самый высокогорный ресторан курорта Шымбулак — на высоте 3 200 метров над уровнем моря. Он продолжает Tenir Eco Hotel и принимает гостей независимо от того, проживают ли они в отеле.</p><p>Меню рассчитано на горный ритм курорта: сытные, выразительные по вкусу блюда, которые помогают восстановить силы после катания. Доступны основное и барное меню.</p><p>Обеды и ужины для групп — по предварительному заказу. Закрытые корпоративные и частные мероприятия проходят в интерьере ресторана или на террасе.</p>',
                'en' => '<p>The highest-altitude restaurant at Shymbulak resort, at 3,200 metres above sea level. It is an extension of Tenir Eco Hotel and welcomes guests whether or not they are staying at the hotel.</p><p>The menu follows the rhythm of the mountains: hearty, richly flavoured dishes to restore your strength after skiing. A main menu and a bar menu are available.</p><p>Group lunches and dinners are arranged by prior order. Private corporate and personal events take place in the restaurant or on the terrace.</p>',
                'kk' => '<p>Шымбұлақ курортындағы ең биік мейрамхана — теңіз деңгейінен 3 200 метр биіктікте. Ол Tenir Eco Hotel-дің жалғасы және қонақүйде тұрса да, тұрмаса да қонақтарды қабылдайды.</p><p>Мәзір курорттың таулық ырғағына сай құрастырылған: шаңғы тепкеннен кейін күш жинауға көмектесетін тойымды, дәмі айқын тағамдар. Негізгі және бар мәзірі бар.</p><p>Топтарға түскі және кешкі астар алдын ала тапсырыспен ұйымдастырылады. Жабық корпоративтік және жеке іс-шаралар мейрамхана интерьерінде немесе террасада өтеді.</p>',
            ]);
            $this->fill($venue, 'hours', [
                'ru' => 'Завтрак для гостей отеля ежедневно 08:30–11:30',
                'en' => 'Breakfast for hotel guests daily 08:30–11:30',
                'kk' => 'Қонақүй қонақтарына таңғы ас күн сайын 08:30–11:30',
            ], ['ru' => ['Завтрак для гостей отеля 08:30–11:30'], 'en' => ['Breakfast for hotel guests 08:30–11:30']]);
            $venue->save();
        }

        // --- FAQ ---
        $faq = [
            ['Как добраться до отеля?', 'How do I get to the hotel?', 'Қонақүйге қалай жетуге болады?',
                '<p>Только по канатной дороге курорта Шымбулак: нижняя станция «Медеу» → гондола до Шымбулака (22–24 мин) → Комби-1 (12–13 мин) → Комби-2 (7–8 мин). От центра Алматы в среднем около 1,5 часов. В выходные возможны очереди.</p>',
                '<p>Only by the Shymbulak cable cars: Medeu lower station → gondola to Shymbulak (22–24 min) → Combi-1 (12–13 min) → Combi-2 (7–8 min). About 1.5 hours from central Almaty on average. Expect queues at weekends.</p>',
                '<p>Тек Шымбұлақ курортының аспалы жолымен: «Медеу» төменгі станциясы → Шымбұлаққа гондола (22–24 мин) → Комби-1 (12–13 мин) → Комби-2 (7–8 мин). Алматы орталығынан орта есеппен шамамен 1,5 сағат. Демалыс күндері кезек болуы мүмкін.</p>'],
            ['Входит ли канатная дорога в стоимость проживания?', 'Is the cable car included in the room rate?', 'Аспалы жол тұру құнына кіре ме?',
                '<p>Нет, проживание не даёт автоматического безлимитного доступа. Гостям доступны разовый пеший билет Tenir (Медеу — Tenir и обратно) и многодневный Multipass. В пакете Romantic Stay билеты на канатную дорогу в обе стороны включены.</p>',
                '<p>No, a stay does not include unlimited access. Guests can use a single Tenir pedestrian ticket (Medeu – Tenir and back) or a multi-day Multipass. The Romantic Stay package includes cable car tickets both ways.</p>',
                '<p>Жоқ, тұру шектеусіз қолжетімділікті қамтымайды. Қонақтарға Tenir бір реттік жаяу билеті (Медеу — Tenir және кері) және көпкүндік Multipass бар. Romantic Stay пакетіне аспалы жолға екі жаққа билеттер кіреді.</p>'],
            ['Что такое Multipass?', 'What is the Multipass?', 'Multipass дегеніміз не?',
                '<p>Многодневный пеший билет для гостей отеля на 3, 5 или 7 дней: гондола от «Медеу» до Комби-2 (Tenir) и свободное передвижение по Комби-1 и Комби-2, до четырёх подъёмов в день. Выгоднее разовых билетов. Электронный билет приходит в день заезда; последняя посадка — за 30 минут до закрытия канатной дороги; для катания пешеходный билет не действует.</p>',
                '<p>A multi-day pedestrian ticket for hotel guests, valid for 3, 5 or 7 days: the gondola from Medeu to Combi-2 (Tenir) and free travel on Combi-1 and Combi-2, up to four ascents a day. Better value than single tickets. The e-ticket arrives on the day of arrival; last boarding is 30 minutes before closing; it is not valid for skiing.</p>',
                '<p>Қонақүй қонақтарына арналған 3, 5 немесе 7 күндік көпкүндік жаяу билет: «Медеуден» Комби-2-ге (Tenir) дейін гондола және Комби-1 мен Комби-2 бойынша еркін жүру, күніне төрт көтерілуге дейін. Бір реттік билеттерден тиімдірек. Электронды билет келген күні жіберіледі; соңғы отырғызу — аспалы жол жабылуынан 30 минут бұрын; шаңғы тебу үшін жарамайды.</p>'],
            ['Как быть с багажом?', 'What about luggage?', 'Жүкпен не істеу керек?',
                '<p>Багаж поднимается на канатной дороге, затем — короткие пешие переходы, поэтому лучше брать лёгкий багаж. Портье заберёт его на станции «Шымбулак» (начало Комби-1) и доставит в номер, а при выезде — к транспорту. Услуга платная (за единицу багажа в одну сторону) и заказывается заранее; супервайзер отеля свяжется с вами перед заездом или выездом.</p>',
                '<p>Luggage goes up by cable car, followed by short walks, so it is best to travel light. A porter collects it at Shymbulak station (the start of Combi-1) and brings it to your room, or to your transport when you leave. The service is paid (per item, one way) and must be booked in advance; the hotel supervisor will contact you before arrival or departure.</p>',
                '<p>Жүк аспалы жолмен көтеріледі, одан әрі қысқа жаяу өтпелер бар, сондықтан жеңіл жүк алған дұрыс. Портье оны «Шымбұлақ» станциясынан (Комби-1 басы) алып, нөмірге жеткізеді, ал шығарда — көлікке дейін. Қызмет ақылы (бір жүк бірлігі үшін бір бағытта) және алдын ала тапсырыс беріледі; қонақүй супервайзері келуге немесе шығуға дейін байланысады.</p>'],
            ['Время заезда и выезда, депозит', 'Check-in, check-out and deposit', 'Кіру, шығу уақыты және депозит',
                '<p>Заезд с 15:30, выезд до 11:00. Для гарантии бронирования требуется полная предоплата. Депозит 50 000 ₸ вносится на стойке регистрации.</p>',
                '<p>Check-in from 15:30, check-out by 11:00. Full prepayment guarantees the booking. A deposit of 50,000 ₸ is paid at the front desk.</p>',
                '<p>Кіру 15:30-дан, шығу 11:00-ге дейін. Брондауды кепілдендіру үшін толық алдын ала төлем қажет. 50 000 ₸ депозит ресепшнде төленеді.</p>'],
            ['Можно ли с детьми?', 'Can I stay with children?', 'Балалармен тұруға бола ма?',
                '<p>Дети от 6 до 14 лет включительно проживают с родителями: доплата 30 000 ₸ за ночь, включая завтрак и отдельное спальное место. Заселение детей младше 6 лет на высоте 3 200 м не разрешено — из-за высокогорья.</p>',
                '<p>Children from 6 to 14 inclusive stay with their parents: 30,000 ₸ per night, including breakfast and a separate bed. Children under 6 cannot be accommodated at 3,200 m because of the altitude.</p>',
                '<p>6 жастан 14 жасқа дейінгі (қоса) балалар ата-анасымен тұрады: түніне 30 000 ₸ үстеме, таңғы ас пен жеке төсек орны қоса. Биіктікке байланысты 6 жасқа дейінгі балаларды 3 200 м-де орналастыруға болмайды.</p>'],
            ['Условия отмены бронирования', 'Cancellation policy', 'Брондаудан бас тарту шарттары',
                '<p>Летний сезон (20.03–31.10): без штрафа за 5 суток до заезда, позже — штраф в размере стоимости первой ночи. Зимний сезон (01.11–19.03): без штрафа за 14 суток до заезда, позже — штраф 100% за весь период. При изменении дат или длительности тариф может измениться.</p>',
                '<p>Summer season (20 Mar – 31 Oct): free cancellation up to 5 days before arrival; later, a penalty equal to the first night. Winter season (1 Nov – 19 Mar): free cancellation up to 14 days before arrival; later, a 100% penalty for the whole stay. If dates or length of stay change, the rate may change.</p>',
                '<p>Жазғы маусым (20.03–31.10): келуге 5 тәулік қалғанда айыппұлсыз, кейін — бірінші түннің құнындай айыппұл. Қысқы маусым (01.11–19.03): келуге 14 тәулік қалғанда айыппұлсыз, кейін — бүкіл кезең үшін 100% айыппұл. Күндер немесе тұру ұзақтығы өзгерсе, тариф өзгеруі мүмкін.</p>'],
            ['Во сколько завтрак и можно ли поужинать в ресторане?', 'When is breakfast, and can I have dinner at the restaurant?', 'Таңғы ас қашан және мейрамханада кешкі ас ішуге бола ма?',
                '<p>Завтрак для гостей отеля — ежедневно с 08:30 до 11:30. Ресторан Tenir на высоте 3 200 м принимает и обеды, ужины; доступны основное и барное меню. «Ужин для двоих» по заказному меню — 60 000 ₸, бронирование за 24 часа.</p>',
                '<p>Breakfast for hotel guests is served daily from 08:30 to 11:30. Tenir restaurant at 3,200 m also serves lunch and dinner, with a main menu and a bar menu. “Dinner for two” from the set menu costs 60,000 ₸; book 24 hours ahead.</p>',
                '<p>Қонақүй қонақтарына таңғы ас — күн сайын 08:30-дан 11:30-ға дейін. 3 200 м биіктіктегі Tenir мейрамханасы түскі және кешкі астарды да ұсынады; негізгі және бар мәзірі бар. Тапсырыс мәзірі бойынша «Екеуіне арналған кешкі ас» — 60 000 ₸, 24 сағат бұрын брондау.</p>'],
            ['Можно ли провести мероприятие или конференцию?', 'Can I hold an event or a conference?', 'Іс-шара немесе конференция өткізуге бола ма?',
                '<p>Да: Купол, приватный зал PDR и две панорамные террасы — для презентаций, мини-конференций на 10–20 человек, тимбилдингов и частных праздников. Корпоративные запросы и туроператоры: +7 701 427 18 48, sales@tenirhotels.com.</p>',
                '<p>Yes: the Dome, the private dining room (PDR) and two panoramic terraces — for presentations, mini-conferences of 10–20 people, team building and private celebrations. Corporate enquiries and tour operators: +7 701 427 18 48, sales@tenirhotels.com.</p>',
                '<p>Иә: Күмбез, PDR жеке залы және екі панорамалық терраса — презентацияларға, 10–20 адамдық шағын конференцияларға, тимбилдингтер мен жеке мерекелерге. Корпоративтік сұраныстар мен туроператорлар: +7 701 427 18 48, sales@tenirhotels.com.</p>'],
        ];
        // Первые два вопроса — это черновики из сидера (sort 1 и 2): обновляем их, остальные добавляем.
        $existing = Faq::where('hotel_id', $hotel->id)->orderBy('sort')->get();
        $sort = (int) $existing->max('sort');
        foreach ($faq as $i => [$qRu, $qEn, $qKk, $aRu, $aEn, $aKk]) {
            $row = [
                'question' => ['ru' => $qRu, 'en' => $qEn, 'kk' => $qKk],
                'answer' => ['ru' => $aRu, 'en' => $aEn, 'kk' => $aKk],
            ];
            $match = $existing->first(fn ($f) => ($f->question['ru'] ?? null) === $qRu);
            if ($match) {
                if (mb_strlen(strip_tags((string) ($match->answer['ru'] ?? ''))) < 160) { // короткий ответ из сидера
                    $match->update($row);
                }
                continue;
            }
            Faq::create($row + ['hotel_id' => $hotel->id, 'is_published' => true, 'sort' => ++$sort]);
        }
        // Вопрос про скидку из сидера оставляем, но приводим к фактам текущего сайта.
        if ($disc = $existing->first(fn ($f) => ($f->question['ru'] ?? null) === 'Есть ли скидка при длительном проживании?')) {
            $disc->update([
                'answer' => ['ru' => '<p>Да, при проживании более трёх ночей — скидка 10% от основного тарифа.</p>', 'en' => '<p>Yes, 10% off the base rate for stays of more than three nights.</p>', 'kk' => '<p>Иә, 3 түннен көп тұрғанда негізгі тарифтен 10% жеңілдік.</p>'],
            ]);
        }
    }

    public function down(): void
    {
        //
    }
};
