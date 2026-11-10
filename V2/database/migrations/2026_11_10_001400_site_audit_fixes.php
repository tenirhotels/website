<?php

use App\Models\Faq;
use App\Models\Hotel;
use App\Models\Page;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Итоги проверки сайта:
 *  1. Билеты Multipass приходят за сутки до заезда (а не в день заезда) — как в «Что важно знать».
 *  2. Казахские (и местами китайские/русские) тексты там, где язык был пуст и показывался чужой.
 *  3. Страницы «Политика конфиденциальности» и «Условия использования»: вместо «[Вставьте текст…]» — нейтральная заметка.
 * Правки редакторов не затираются: заполняются только пустые языки; тексты ищутся по точному совпадению.
 */
return new class extends Migration
{
    /** Исходная фраза (ru или en) → недостающие языки. Переводы kk и zh — черновики, нужна вычитка носителем. */
    private const FILL = [
        'Самый высокогорный эко-отель Казахстана: десять отдельных модулей на высоте 3 200 м среди ледников Заилийского Алатау.' => [
            'kk' => 'Қазақстандағы ең биік таулы эко-қонақүй: Іле Алатауының мұздықтары арасында, 3 200 м биіктікте орналасқан он жеке модуль.',
        ],
        'Панорамное окно во всю стену на хребты Заилийского Алатау и собственная финская сауна.' => [
            'kk' => 'Іле Алатауының жоталарына қарайтын бүкіл қабырғаны алатын панорамалық терезе және жеке фин сауна.',
        ],
        'Авторская кухня на высоте 3 200 м: завтраки и ужины по специальному меню рядом с модулями.' => [
            'kk' => '3 200 м биіктіктегі авторлық тағамдар: модульдердің қасында арнайы мәзір бойынша таңғы және кешкі астар.',
        ],
        'Проживание с завтраком и ужином в ресторане отеля по специальному меню.' => [
            'kk' => 'Қонақүй мейрамханасында арнайы мәзір бойынша таңғы және кешкі асы бар тұру.',
        ],
        'Номер оформлен в стиле «Романтические моменты», бутылка вина в комплимент.' => [
            'kk' => 'Бөлме «Романтикалық сәттер» стилінде безендірілген, комплимент ретінде бір бөтелке шарап.',
        ],
        'Часовая прогулка верхом по горным склонам.' => [
            'kk' => 'Тау беткейлерімен бір сағаттық атпен серуен.',
        ],
        'Пакет без проживания: съёмка среди ледников и облаков.' => [
            'kk' => 'Түнеусіз пакет: мұздықтар мен бұлттар арасындағы фотосъёмка.',
        ],
        'Есть ли скидка при длительном проживании?' => [
            'kk' => 'Ұзақ тұрғанда жеңілдік бар ма?',
            'zh' => '长住有优惠吗？',
        ],
        'Политика конфиденциальности' => ['kk' => 'Құпиялылық саясаты', 'zh' => '隐私政策'],
        'Условия использования' => ['kk' => 'Пайдалану шарттары', 'zh' => '使用条款'],
        // Фразы, которые редактор ввёл только по-английски: на русской и казахской страницах показывался английский.
        'the highest-altitude eco hotel in Kazakhstan' => [
            'ru' => 'Самый высокогорный эко-отель Казахстана',
            'kk' => 'Қазақстандағы ең биік таулы эко-қонақүй',
            'zh' => '哈萨克斯坦海拔最高的生态酒店',
        ],
        'Above the ordinary. Closer to nature.' => [
            'ru' => 'Выше обыденного. Ближе к природе.',
            'kk' => 'Қарапайымнан биік. Табиғатқа жақын.',
            'zh' => '超越寻常，亲近自然。',
        ],
    ];

    /** Удобства номера (русский список → казахский); незнакомые пункты остаются как есть. */
    private const AMENITIES_KK = [
        'Финская сауна в номере' => 'Бөлмедегі фин сауна', 'Финская сауна' => 'Фин сауна', 'Панорамное окно' => 'Панорамалық терезе',
        'Отдельный модуль' => 'Жеке модуль', 'Кровать king-size' => 'King-size керует', 'Бесплатный Wi-Fi' => 'Тегін Wi-Fi',
        'Телевизор, спутниковые каналы' => 'Теледидар, спутниктік арналар', 'Мини-бар' => 'Мини-бар',
        'Кофеварка и чайник' => 'Кофе қайнатқыш және шәйнек', 'Завтрак в номер' => 'Таңғы ас бөлмеге',
        'Душ и косметические принадлежности' => 'Душ және косметикалық құралдар', 'Халат, тапочки, фен' => 'Халат, тәпішке, шаш кептіргіш',
        'Локер для спортивного инвентаря' => 'Спорттық құрал-жабдыққа арналған шкаф', 'Панорама на горы' => 'Тауға қарайтын панорама',
        'Ski-in / ski-out' => 'Ski-in / ski-out',
    ];

    private const TABLES = ['hotels', 'room_types', 'venues', 'experiences', 'offers', 'events', 'faqs', 'pages', 'locations'];

    public function up(): void
    {
        $this->tickets();
        $this->fillLanguages();
        $this->legalPages();
    }

    private function tickets(): void
    {
        $hotel = Hotel::where('slug', 'shymbulak')->first();
        if (! $hotel) {
            return;
        }
        $map = [
            'ru' => ['Электронный билет приходит в день заезда', 'Электронный билет приходит за сутки до заезда'],
            'en' => ['The e-ticket arrives on the day of arrival', 'The e-ticket arrives the day before arrival'],
            'kk' => ['Электронды билет келген күні жіберіледі', 'Электронды билет келуге бір тәулік қалғанда жіберіледі'],
        ];
        foreach (Faq::where('hotel_id', $hotel->id)->get() as $faq) {
            $a = (array) $faq->answer;
            $changed = false;
            foreach ($map as $l => [$from, $to]) {
                if (isset($a[$l]) && str_contains($a[$l], $from)) {
                    $a[$l] = str_replace($from, $to, $a[$l]);
                    $changed = true;
                }
            }
            if ($changed) {
                $faq->answer = $a;
                $faq->save();
            }
        }
    }

    private function fillLanguages(): void
    {
        foreach (self::TABLES as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }
            foreach (DB::table($table)->get() as $row) {
                $update = [];
                foreach ((array) $row as $col => $value) {
                    if (! is_string($value) || ! in_array($value[0] ?? '', ['{', '['], true)) {
                        continue;
                    }
                    $data = json_decode($value, true);
                    if (! is_array($data)) {
                        continue;
                    }
                    $changed = false;
                    $this->walk($data, $changed);
                    if ($changed) {
                        $update[$col] = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }
                }
                if ($update) {
                    DB::table($table)->where('id', $row->id)->update($update);
                }
            }
        }
    }

    private function walk(array &$node, bool &$changed): void
    {
        foreach (['ru', 'en'] as $src) {
            if (isset($node[$src]) && is_string($node[$src]) && isset(self::FILL[$node[$src]])) {
                foreach (self::FILL[$node[$src]] as $locale => $text) {
                    if (blank($node[$locale] ?? null)) {
                        $node[$locale] = $text;
                        $changed = true;
                    }
                }
            }
        }
        // Список удобств по языкам: {"ru": [...], "en": [...]} — казахский список по русскому.
        if (isset($node['ru']) && is_array($node['ru']) && array_is_list($node['ru']) && blank($node['kk'] ?? null)
            && $node['ru'] === array_filter($node['ru'], 'is_string') && count(array_intersect($node['ru'], array_keys(self::AMENITIES_KK))) >= 3) {
            $node['kk'] = array_map(fn ($item) => self::AMENITIES_KK[$item] ?? $item, $node['ru']);
            $changed = true;
        }
        foreach ($node as &$child) {
            if (is_array($child)) {
                $this->walk($child, $changed);
            }
        }
    }

    private function legalPages(): void
    {
        $note = [
            'ru' => 'Документ готовится к публикации. По вопросам об условиях и обработке персональных данных напишите на <a href="mailto:reservations@tenirhotels.com">reservations@tenirhotels.com</a>.',
            'en' => 'This document is being prepared for publication. For questions about the terms and the processing of personal data, please write to <a href="mailto:reservations@tenirhotels.com">reservations@tenirhotels.com</a>.',
            'kk' => 'Құжат жариялауға дайындалуда. Шарттар мен жеке деректерді өңдеуге қатысты сұрақтар бойынша <a href="mailto:reservations@tenirhotels.com">reservations@tenirhotels.com</a> мекенжайына жазыңыз.',
            'zh' => '该文件正在准备发布。有关条款及个人数据处理的问题，请发送邮件至 <a href="mailto:reservations@tenirhotels.com">reservations@tenirhotels.com</a>。',
        ];
        foreach (Page::whereIn('slug', ['privacy', 'terms'])->get() as $page) {
            $content = (array) $page->content;
            $placeholder = false;
            foreach ($content as $blocks) {
                foreach ((array) $blocks as $block) {
                    $body = (string) ($block['data']['body'] ?? '');
                    $placeholder = $placeholder || str_contains($body, '[Вставьте текст') || str_contains($body, '[Paste the text');
                }
            }
            if (! $placeholder) {
                continue; // редактор уже внёс настоящий текст
            }
            foreach ($note as $locale => $text) {
                $content[$locale] = [['type' => 'text', 'data' => ['heading' => null, 'body' => '<p>'.$text.'</p>']]];
            }
            $page->content = $content;
            $page->save();
        }
    }

    public function down(): void
    {
    }
};
