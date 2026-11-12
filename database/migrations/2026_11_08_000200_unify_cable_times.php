<?php

use App\Models\Faq;
use App\Models\Hotel;
use Illuminate\Database\Migrations\Migration;

/**
 * Единые времена в пути — как на карте курорта (ResortMap: 22 / 17 / 9 мин, ≈ 60 мин с пересадками),
 * и без повтора правила «посадка за 30 минут» в ответе про Multipass (оно есть в графике на странице).
 */
return new class extends Migration
{
    public function up(): void
    {
        $hotel = Hotel::where('slug', 'shymbulak')->first();
        if (! $hotel) {
            return;
        }

        $route = [
            'ru' => '<p>Только по канатной дороге курорта Шымбулак: станция «Медеу» → Шымбулак (≈ 22 мин) → Combi-1 (≈ 17 мин) → Combi-2 (≈ 9 мин). В одну сторону около часа с пересадками. В выходные возможны очереди.</p>',
            'en' => '<p>Only by the Shymbulak cable cars: Medeu station → Shymbulak (≈ 22 min) → Combi-1 (≈ 17 min) → Combi-2 (≈ 9 min). About an hour one way, including changes. Expect queues at weekends.</p>',
            'kk' => '<p>Тек Шымбұлақ курортының аспалы жолымен: «Медеу» станциясы → Шымбұлақ (≈ 22 мин) → Combi-1 (≈ 17 мин) → Combi-2 (≈ 9 мин). Бір бағытқа ауысуларымен бірге шамамен бір сағат. Демалыс күндері кезек болуы мүмкін.</p>',
        ];
        $multipass = [
            'ru' => ['Электронный билет приходит в день заезда; последняя посадка — за 30 минут до закрытия канатной дороги; для катания пешеходный билет не действует.', 'Для катания пешеходный билет не действует.'],
            'en' => ['The e-ticket arrives on the day of arrival; last boarding is 30 minutes before closing; it is not valid for skiing.', 'It is not valid for skiing.'],
            'kk' => ['Электронды билет келген күні жіберіледі; соңғы отырғызу — аспалы жол жабылуынан 30 минут бұрын; шаңғы тебу үшін жарамайды.', 'Шаңғы тебу үшін жарамайды.'],
        ];

        foreach (Faq::where('hotel_id', $hotel->id)->get() as $faq) {
            $q = (string) ($faq->question['ru'] ?? '');
            $a = (array) $faq->answer;
            if ($q === 'Как добраться до отеля?') {
                $faq->answer = array_merge($a, $route);
                $faq->save();
            } elseif ($q === 'Что такое Multipass?') {
                foreach ($multipass as $l => [$from, $to]) {
                    if (isset($a[$l])) {
                        $a[$l] = str_replace($from, $to, $a[$l]);
                    }
                }
                $faq->answer = $a;
                $faq->save();
            }
        }
    }

    public function down(): void
    {
    }
};
