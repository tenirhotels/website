<?php

use App\Models\Hotel;
use App\Models\Redirect;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Страница «Перед приездом»: текст «Что важно знать» живёт в локации (канатная дорога, билеты, одежда — это место),
 * вопросы и ответы — у отеля. Блок переезжает из «Как добраться»; старая страница «Information» ведёт на новую.
 */
return new class extends Migration
{
    private const HEADINGS = ['<h3>Что важно знать</h3>', '<h3>Good to know</h3>', '<h3>Білу маңызды</h3>'];

    public function up(): void
    {
        if (! Schema::hasColumn('locations', 'arrival_info')) {
            Schema::table('locations', function (Blueprint $table) {
                $table->json('arrival_info')->nullable()->after('getting_here');
            });
        }

        $location = Hotel::where('slug', 'shymbulak')->first()?->location;
        if ($location) {
            $info = (array) $location->arrival_info;
            $here = (array) $location->getting_here;
            foreach (['ru', 'en', 'kk'] as $l) {
                $text = (string) ($here[$l] ?? '');
                if (filled($info[$l] ?? null) || ! str_starts_with($text, self::HEADINGS[array_search($l, ['ru', 'en', 'kk'])])) {
                    continue;
                }
                // Блок — до закрывающего </ul>; всё, что редактор дописал ниже, остаётся в «Как добраться».
                $end = strpos($text, '</ul>');
                $end = $end === false ? strlen($text) : $end + 5;
                $info[$l] = substr($text, 0, $end);
                $here[$l] = trim(substr($text, $end));
            }
            if (blank($info['zh'] ?? null) && filled($info['ru'] ?? null)) {
                $info['zh'] = '<h3>须知</h3><ul>'
                    .'<li>住宿费不含缆车费用。住客可购买 Tenir 单次步行票（Medeu — Tenir 往返）或 3、5、7 天的 Multipass：每天最多上行四次，比单次票更划算。Romantic Stay 套餐含双程缆车票。</li>'
                    .'<li>电子票于入住当天发送至订单中的联系方式。在其他地方购买、次日使用的票无效。步行票不适用于滑雪。</li>'
                    .'<li>行李由缆车运送，之后还有短距离步行，因此请轻装出行。礼宾员可从 Shymbulak 站协助将行李送至客房并在退房时送回，需额外付费，请提前预约。</li>'
                    .'<li>高海拔地区气温较低，请根据天气着装。</li></ul>';
            }
            $location->arrival_info = $info;
            $location->getting_here = $here;
            $location->save();
        }

        // Старая страница «Information» сайта Exely — это и есть «Перед приездом».
        foreach (['ru-ru' => 'ru', 'kk-kz' => 'kk', 'en-gb' => 'en', 'zh-cn' => 'zh'] as $old => $new) {
            Redirect::updateOrCreate(
                ['from_path' => Redirect::normalize("/$old/information")],
                ['to_path' => "/$new/before-you-arrive", 'status_code' => 301, 'is_active' => true],
            );
        }
    }

    public function down(): void
    {
        Schema::table('locations', fn (Blueprint $table) => $table->dropColumn('arrival_info'));
    }
};
