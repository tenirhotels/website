<?php

use App\Models\Faq;
use App\Models\Hotel;
use Illuminate\Database\Migrations\Migration;

/** Порядок вопросов на «Перед приездом»: сначала то, что нужно знать до поездки; скидка — в конце. */
return new class extends Migration
{
    public function up(): void
    {
        $hotel = Hotel::where('slug', 'shymbulak')->first();
        if (! $hotel) {
            return;
        }
        $order = [
            'Как добраться до отеля?', 'Входит ли канатная дорога в стоимость проживания?', 'Что такое Multipass?',
            'Как быть с багажом?', 'Время заезда и выезда, депозит', 'Можно ли с детьми?', 'Условия отмены бронирования',
            'Во сколько завтрак и можно ли поужинать в ресторане?', 'Можно ли провести мероприятие или конференцию?',
            'Есть ли скидка при длительном проживании?',
        ];
        foreach (Faq::where('hotel_id', $hotel->id)->get() as $faq) {
            $i = array_search($faq->question['ru'] ?? '', $order, true);
            if ($i !== false) {
                $faq->update(['sort' => $i + 1]);
            }
        }
    }

    public function down(): void
    {
    }
};
