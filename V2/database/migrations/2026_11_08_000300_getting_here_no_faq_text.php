<?php

use App\Models\Hotel;
use Illuminate\Database\Migrations\Migration;

/** Вопросов и ответов на странице «Как добраться» больше нет — главное про билеты и багаж живёт в тексте. */
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
                .'<li>Проживание не включает проезд по канатной дороге. Для гостей есть разовый пеший билет Tenir (Медеу — Tenir и обратно) и Multipass на 3, 5 или 7 дней: до четырёх подъёмов в день, выгоднее разовых билетов. В пакете Romantic Stay билеты в обе стороны включены.</li>'
                .'<li>Электронный билет приходит в день заезда на контакты из брони. Билеты, купленные в другом месте на следующий день, не действуют. Для катания пешеходный билет не подходит.</li>'
                .'<li>Багаж поднимается на канатных дорогах, дальше — короткие пешие переходы, поэтому берите лёгкий багаж. Портье поможет от станции «Шымбулак» до номера и обратно за дополнительную плату — заказывайте заранее.</li>'
                .'<li>На высоте прохладнее: одежда по погоде.</li></ul>',
            'en' => '<h3>Good to know</h3><ul>'
                .'<li>A stay does not include the cable car. Guests can use a single Tenir pedestrian ticket (Medeu – Tenir and back) or a Multipass for 3, 5 or 7 days: up to four ascents a day, better value than single tickets. The Romantic Stay package includes tickets both ways.</li>'
                .'<li>The e-ticket is sent on the day of arrival to the contacts in your booking. Tickets bought elsewhere do not work the next day. A pedestrian ticket is not valid for skiing.</li>'
                .'<li>Luggage goes up by cable car, followed by short walks, so pack light. A porter can help from Shymbulak station to your room and back for an extra fee — please book in advance.</li>'
                .'<li>It is cooler at altitude: dress for the weather.</li></ul>',
            'kk' => '<h3>Білу маңызды</h3><ul>'
                .'<li>Тұру аспалы жолды қамтымайды. Қонақтарға Tenir бір реттік жаяу билеті (Медеу — Tenir және кері) немесе 3, 5, 7 күндік Multipass бар: күніне төрт көтерілуге дейін, бір реттік билеттерден тиімдірек. Romantic Stay пакетіне екі жаққа билеттер кіреді.</li>'
                .'<li>Электронды билет келген күні брондағы байланыс деректеріне жіберіледі. Басқа жерден сатып алынған билеттер келесі күні жарамайды. Жаяу билет шаңғы тебуге жарамайды.</li>'
                .'<li>Жүк аспалы жолмен көтеріледі, одан әрі қысқа жаяу өтпелер бар, сондықтан жеңіл жүк алыңыз. Портье «Шымбұлақ» станциясынан нөмірге дейін және кері қосымша ақыға көмектеседі — алдын ала тапсырыс беріңіз.</li>'
                .'<li>Биіктікте салқынырақ: ауа райына сай киініңіз.</li></ul>',
        ];

        $current = (array) $hotel->getting_here;
        foreach ($new as $locale => $html) {
            if (str_contains((string) ($current[$locale] ?? ''), 'вопросах ниже') || str_contains((string) ($current[$locale] ?? ''), 'questions below') || str_contains((string) ($current[$locale] ?? ''), 'сұрақтардан')) {
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
