<?php

use App\Models\Hotel;
use Illuminate\Database\Migrations\Migration;

/** Возвращаем правило про билеты, купленные в другом месте. */
return new class extends Migration
{
    public function up(): void
    {
        $hotel = Hotel::where('slug', 'shymbulak')->first();
        if (! $hotel) {
            return;
        }

        $pairs = [
            'ru' => ['доступные отелю. Для катания', 'доступные отелю. Билеты, купленные в другом месте на следующий день, не действуют. Для катания'],
            'en' => ['available to the hotel. A pedestrian', 'available to the hotel. Tickets bought elsewhere do not work the next day. A pedestrian'],
            'kk' => ['қолжетімді байланыс деректері бойынша жібереміз. Жаяу', 'қолжетімді байланыс деректері бойынша жібереміз. Басқа жерден сатып алынған билеттер келесі күні жарамайды. Жаяу'],
        ];

        $current = (array) $hotel->getting_here;
        foreach ($pairs as $locale => [$from, $to]) {
            $text = (string) ($current[$locale] ?? '');
            if (str_contains($text, $from) && ! str_contains($text, $to)) {
                $current[$locale] = str_replace($from, $to, $text);
            }
        }
        $hotel->getting_here = $current;
        $hotel->save();
    }

    public function down(): void
    {
    }
};
