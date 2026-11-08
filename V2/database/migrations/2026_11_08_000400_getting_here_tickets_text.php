<?php

use App\Models\Hotel;
use Illuminate\Database\Migrations\Migration;

/** «Как добраться»: уточнённый текст про билеты, багаж и одежду. Заменяется только прежний текст из миграции 000300. */
return new class extends Migration
{
    public function up(): void
    {
        $hotel = Hotel::where('slug', 'shymbulak')->first();
        if (! $hotel) {
            return;
        }

        $new = [
            'ru' => '<h3>Что важно знать</h3><ul>'
                .'<li><strong>Билеты.</strong> Проезд по канатной дороге не входит в проживание автоматически. Некоторые тарифы уже включают билеты — например, пакет Romantic Stay: это выгоднее. Если билеты не включены, для гостей есть разовый пеший билет Tenir (Медеу — Tenir и обратно) и Multipass на 3, 5 или 7 дней — до четырёх подъёмов в день.</li>'
                .'<li>Если билеты включены в проживание, за сутки до заезда мы высылаем их на контактные данные, доступные отелю. Для катания пешеходный билет не подходит.</li>'
                .'<li><strong>Багаж.</strong> До отеля три канатные дороги и короткие пешие переходы, поэтому с большим багажом непросто — рекомендуем ехать налегке. Если понадобится помощь портье (от станции «Шымбулак» до номера и обратно), заранее сообщите об этом ресепшену. Услуга платная.</li>'
                .'<li><strong>Одежда.</strong> На высоте прохладнее: одевайтесь по погоде.</li></ul>',
            'en' => '<h3>Good to know</h3><ul>'
                .'<li><strong>Tickets.</strong> The cable car is not automatically included in your stay. Some rates already include tickets — for example, the Romantic Stay package — which is better value. If tickets are not included, guests can use a single Tenir pedestrian ticket (Medeu – Tenir and back) or a Multipass for 3, 5 or 7 days — up to four ascents a day.</li>'
                .'<li>If tickets are included in your stay, we send them the day before arrival to the contact details available to the hotel. A pedestrian ticket is not valid for skiing.</li>'
                .'<li><strong>Luggage.</strong> The way to the hotel takes three cable cars and short walks, so large luggage is hard to manage — we recommend travelling light. If you need a porter (from Shymbulak station to your room and back), please tell the front desk in advance. This is a paid service.</li>'
                .'<li><strong>Clothing.</strong> It is cooler at altitude: dress for the weather.</li></ul>',
            'kk' => '<h3>Білу маңызды</h3><ul>'
                .'<li><strong>Билеттер.</strong> Аспалы жолмен жүру тұру құнына автоматты түрде кірмейді. Кейбір тарифтер билеттерді қамтиды — мысалы, Romantic Stay пакеті: бұл тиімдірек. Билеттер қамтылмаса, қонақтарға Tenir бір реттік жаяу билеті (Медеу — Tenir және кері) немесе 3, 5, 7 күндік Multipass бар — күніне төрт көтерілуге дейін.</li>'
                .'<li>Билеттер тұру құнына кірсе, оларды келуге бір тәулік қалғанда қонақүйге қолжетімді байланыс деректері бойынша жібереміз. Жаяу билет шаңғы тебуге жарамайды.</li>'
                .'<li><strong>Жүк.</strong> Қонақүйге дейін үш аспалы жол және қысқа жаяу өтпелер бар, сондықтан үлкен жүкпен қиын — жеңіл келуді ұсынамыз. Портье көмегі («Шымбұлақ» станциясынан нөмірге дейін және кері) қажет болса, алдын ала ресепшенге хабарлаңыз. Қызмет ақылы.</li>'
                .'<li><strong>Киім.</strong> Биіктікте салқынырақ: ауа райына сай киініңіз.</li></ul>',
        ];

        $current = (array) $hotel->getting_here;
        foreach ($new as $locale => $html) {
            $text = (string) ($current[$locale] ?? '');
            if (str_contains($text, 'Multipass') && (str_contains($text, 'Проживание не включает') || str_contains($text, 'A stay does not include') || str_contains($text, 'Тұру аспалы жолды'))) {
                $current[$locale] = $html;
            }
        }
        $hotel->getting_here = $current;
        $hotel->save();
    }

    public function down(): void
    {
    }
};
