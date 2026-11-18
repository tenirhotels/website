<?php

use App\Models\Faq;
use App\Models\Hotel;
use Illuminate\Database\Migrations\Migration;

/**
 * «Как добраться»: маршрут, график и карта теперь выводятся блоками страницы,
 * поэтому в тексте отеля остаётся только то, чего там нет. Правки редакторов не трогаем:
 * заменяется лишь импортированный ранее текст с разделом «Маршрут» или черновик из сидера.
 */
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
                .'<li>Проживание не включает проезд по канатной дороге: для гостей есть разовый пеший билет Tenir или Multipass — подробнее в вопросах ниже.</li>'
                .'<li>Электронный билет приходит в день заезда на контакты из брони. Билеты, купленные в другом месте на следующий день, не действуют.</li>'
                .'<li>На высоте прохладнее: одежда по погоде.</li></ul>',
            'en' => '<h3>Good to know</h3><ul>'
                .'<li>A stay does not include the cable car: guests can use a single Tenir pedestrian ticket or a Multipass — see the questions below.</li>'
                .'<li>The e-ticket is sent on the day of arrival to the contacts in your booking. Tickets bought elsewhere do not work the next day.</li>'
                .'<li>It is cooler at altitude: dress for the weather.</li></ul>',
            'kk' => '<h3>Білу маңызды</h3><ul>'
                .'<li>Тұру аспалы жолды қамтымайды: қонақтарға Tenir бір реттік жаяу билеті немесе Multipass бар — төмендегі сұрақтардан қараңыз.</li>'
                .'<li>Электронды билет келген күні брондағы байланыс деректеріне жіберіледі. Басқа жерден сатып алынған билеттер келесі күні жарамайды.</li>'
                .'<li>Биіктікте салқынырақ: ауа райына сай киініңіз.</li></ul>',
        ];

        $current = (array) $hotel->getting_here;
        foreach ($new as $locale => $html) {
            $text = (string) ($current[$locale] ?? '');
            if (preg_match('/<h3>(Маршрут|The route|Бағыт)<\/h3>/u', $text) || str_contains($text, '[') || mb_strlen(strip_tags($text)) < 400) {
                $current[$locale] = $html;
            }
        }
        $hotel->getting_here = $current;
        $hotel->save();

        // Ответ на «Как добраться до отеля?» дублирует блоки страницы выше.
        Faq::where('hotel_id', $hotel->id)->get()
            ->filter(fn (Faq $f) => ($f->question['ru'] ?? '') === 'Как добраться до отеля?')
            ->each(fn (Faq $f) => $f->update(['is_published' => false]));
    }

    public function down(): void
    {
    }
};
