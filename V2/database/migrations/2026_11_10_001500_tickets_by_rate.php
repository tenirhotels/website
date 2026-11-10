<?php

use App\Models\Faq;
use App\Models\Hotel;
use Illuminate\Database\Migrations\Migration;

/**
 * «Что важно знать» → «Билеты»: канатная дорога зависит от тарифа (с билетами / без билетов / ски-пасс или сезонный
 * абонемент). Заменяются только первые два пункта списка; багаж, одежда и всё, что редактор дописал ниже, остаются.
 * Ответ FAQ про стоимость канатной дороги приводится к тому же тексту, если он ещё стандартный.
 */
return new class extends Migration
{
    private const TICKETS = [
        'ru' => '<li><p><strong>Билеты.</strong> Проезд по канатной дороге зависит от тарифа, поэтому перед бронированием посмотрите, что входит в ваш:</p><ul>'
            .'<li><strong>Тариф с билетами.</strong> Билеты на канатную дорогу уже включены (например, пакет Romantic Stay). За сутки до заезда мы высылаем их на контактные данные, доступные отелю.</li>'
            .'<li><strong>Тариф без билетов.</strong> В цену входит только проживание, проезд оплачивается отдельно. Для гостей есть разовый пеший билет Tenir (Медеу — Tenir и обратно) и Multipass на 3, 5 или 7 дней — до четырёх подъёмов в день.</li>'
            .'<li><strong>Если вы катаетесь.</strong> Ски-пасс или сезонный абонемент курорта может покрывать подъём, и тогда отдельный билет не нужен. Напишите нам до приезда — подскажем, действует ли ваш пасс на линиях до отеля.</li></ul></li>'
            .'<li><p>Билеты, купленные в другом месте на следующий день, не действуют. Для катания пешеходный билет не подходит.</p></li>',
        'en' => '<li><p><strong>Tickets.</strong> The cable car fare depends on your rate, so please check what your rate includes before booking:</p><ul>'
            .'<li><strong>Rate with tickets.</strong> Cable car tickets are already included (for example, the Romantic Stay package). We send them the day before arrival to the contact details available to the hotel.</li>'
            .'<li><strong>Rate without tickets.</strong> The price covers the stay only; the cable car is paid separately. Guests can use a single Tenir pedestrian ticket (Medeu — Tenir and back) or a Multipass for 3, 5 or 7 days — up to four ascents a day.</li>'
            .'<li><strong>If you ski.</strong> A ski pass or a seasonal pass for the resort may cover the ride, in which case you do not need a separate ticket. Write to us before you arrive and we will tell you whether your pass is valid on the lines to the hotel.</li></ul></li>'
            .'<li><p>Tickets bought elsewhere for the next day are not valid. A pedestrian ticket is not valid for skiing.</p></li>',
        'kk' => '<li><p><strong>Билеттер.</strong> Аспалы жолмен жүру құны тарифке байланысты, сондықтан брондау алдында тарифіңізге не кіретінін қараңыз:</p><ul>'
            .'<li><strong>Билеті бар тариф.</strong> Аспалы жолға билеттер құнына кіреді (мысалы, Romantic Stay пакеті). Біз оларды келуге бір тәулік қалғанда қонақүйге белгілі байланыс деректеріне жібереміз.</li>'
            .'<li><strong>Билетсіз тариф.</strong> Бағаға тек тұру кіреді, жол ақысы бөлек төленеді. Қонақтарға Tenir бір реттік жаяу билеті (Медеу — Tenir және кері) және 3, 5 не 7 күндік Multipass бар — күніне төрт көтерілуге дейін.</li>'
            .'<li><strong>Шаңғы тепсеңіз.</strong> Курорттың ски-пасы немесе маусымдық абонементі көтерілуді қамтуы мүмкін, онда бөлек билет қажет емес. Келмес бұрын бізге жазыңыз — пасыңыздың қонақүйге дейінгі желілерде жарайтынын айтамыз.</li></ul></li>'
            .'<li><p>Басқа жерден келесі күнге сатып алынған билеттер жарамайды. Шаңғы тебу үшін жаяу билет жарамайды.</p></li>',
        'zh' => '<li><p>缆车费用取决于您的房价类型，预订前请确认所选房价包含的内容：</p><ul>'
            .'<li><strong>含票房价。</strong>已包含缆车票（例如 Romantic Stay 套餐）。入住前一天，我们会将票发送至酒店掌握的联系方式。</li>'
            .'<li><strong>不含票房价。</strong>价格仅为住宿费，缆车费用另付。住客可购买 Tenir 单次步行票（Medeu — Tenir 往返）或 3、5、7 天的 Multipass——每天最多上行四次。</li>'
            .'<li><strong>如果您滑雪。</strong>度假区的滑雪票或季票可能涵盖上山缆车，此时无需另购车票。请在抵达前联系我们，我们会告诉您您的票是否适用于通往酒店的线路。</li></ul></li>'
            .'<li><p>在其他地方购买的次日票无效。步行票不适用于滑雪。</p></li>',
    ];

    private const DONE = ['Тариф с билетами', 'Rate with tickets', 'Билеті бар тариф', '含票房价'];

    private const FAQ = [
        'ru' => ['Нет, проживание не даёт', '<p>Зависит от тарифа. Некоторые тарифы (например, пакет Romantic Stay) уже включают билеты на канатную дорогу. В других в цену входит только проживание, а проезд оплачивается отдельно: можно взять разовый пеший билет Tenir (Медеу — Tenir и обратно) или многодневный Multipass. Если вы катаетесь, вам может подойти ски-пасс или сезонный абонемент курорта — уточните у нас до приезда.</p>'],
        'en' => ['No, a stay does not include', '<p>It depends on the rate. Some rates (for example, the Romantic Stay package) already include cable car tickets. With others the price covers the stay only and the cable car is paid separately: you can take a single Tenir pedestrian ticket (Medeu – Tenir and back) or a multi-day Multipass. If you ski, a ski pass or a seasonal pass for the resort may suit you — please check with us before you arrive.</p>'],
        'kk' => ['Жоқ, тұру шектеусіз', '<p>Тарифке байланысты. Кейбір тарифтерге (мысалы, Romantic Stay пакеті) аспалы жолға билеттер кіреді. Басқаларында бағаға тек тұру кіреді, жол ақысы бөлек төленеді: Tenir бір реттік жаяу билетін (Медеу — Tenir және кері) немесе көпкүндік Multipass алуға болады. Шаңғы тепсеңіз, курорттың ски-пасы немесе маусымдық абонементі жарауы мүмкін — келмес бұрын бізден нақтылаңыз.</p>'],
    ];

    public function up(): void
    {
        $hotel = Hotel::where('slug', 'shymbulak')->first();
        if (! $hotel) {
            return;
        }

        $location = $hotel->location;
        if ($location) {
            $info = (array) $location->arrival_info;
            $changed = false;
            foreach (self::TICKETS as $locale => $markup) {
                $text = (string) ($info[$locale] ?? '');
                if ($text === '' || collect(self::DONE)->contains(fn ($m) => str_contains($text, $m))) {
                    continue;
                }
                $new = preg_replace('~^(<h3>[^<]*</h3><ul>)<li>.*?</li><li>.*?</li>~su', '$1'.strtr($markup, ['\\' => '\\\\', '$' => '\\$']), $text, 1, $count);
                if ($count === 1) {
                    $info[$locale] = $new;
                    $changed = true;
                }
            }
            if ($changed) {
                $location->arrival_info = $info;
                $location->save();
            }
        }

        foreach (Faq::where('hotel_id', $hotel->id)->get() as $faq) {
            if (($faq->question['ru'] ?? '') !== 'Входит ли канатная дорога в стоимость проживания?') {
                continue;
            }
            $answer = (array) $faq->answer;
            foreach (self::FAQ as $locale => [$start, $html]) {
                if (str_starts_with(strip_tags((string) ($answer[$locale] ?? '')), $start)) {
                    $answer[$locale] = $html;
                }
            }
            $faq->answer = $answer;
            $faq->save();
        }
    }

    public function down(): void
    {
    }
};
