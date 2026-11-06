<?php

namespace Database\Seeders\Concerns;

/** Тексты страницы Sustainability (сидер и миграция обновления берут их отсюда). */
class SustainabilityContent
{
    public static function summary(): array
    {
        return [
            'ru' => 'Забота о горах — часть сервиса Tenir.',
            'en' => 'Caring for the mountains is part of the Tenir service.',
            'kk' => 'Тауға қамқор болу — Tenir сервисінің бір бөлігі.',
        ];
    }

    /** Заголовок первого блока: по нему миграция понимает, что страницу ещё не правили вручную. */
    public const OLD_RU_HEADING = 'Экологическая безопасность — высший приоритет Tenir';

    public static function content(): array
    {
        return [
            'ru' => [
                ['type' => 'intro', 'data' => [
                    'eyebrow' => 'Наш принцип',
                    'heading' => 'В горах мы в гостях',
                    'text' => 'Устойчивое развитие — основа Tenir: мы принимаем гостей, не нанося вреда природной экосистеме. Забота о природе для нас — часть сервиса, а не дополнительная опция.',
                ]],
                ['type' => 'pillars', 'data' => [
                    'eyebrow' => 'Здание',
                    'heading' => 'Бережно к скале и ландшафту',
                    'items' => [
                        ['title' => 'Эко-материалы', 'text' => 'Фасады из лиственничной дранки сохраняют естественный облик ландшафта и защищают конструкцию от влаги, солнца и ветра.'],
                        ['title' => 'Без фундаментов', 'text' => 'Модули стоят на металлическом основании, закреплённом в скале, — без капитальных конструкций и с минимальным вмешательством в рельеф.'],
                        ['title' => 'Тепло без потерь', 'text' => 'Многослойные сэндвич-панели минимизируют теплопотери и заметно снижают расход электроэнергии на отопление.'],
                        ['title' => 'Энергосбережение', 'text' => 'Долговечное LED-освещение с низким потреблением во всех модулях.'],
                    ],
                ]],
                ['type' => 'pillars', 'data' => [
                    'eyebrow' => 'Каждый день',
                    'heading' => 'Меньше отходов, бережнее к ресурсам',
                    'items' => [
                        ['title' => 'Диспенсеры вместо мини-флаконов', 'text' => 'В ванных комнатах шампунь, гель и кондиционер — из диспенсеров с рефиллами, без одноразовых пластиковых флаконов.'],
                        ['title' => 'Мыло в бумажной обёртке', 'text' => 'Никакой лишней пластиковой упаковки в номере.'],
                        ['title' => 'Полотенца — по вашему желанию', 'text' => 'Меняем их по просьбе гостя: это экономит воду и средства для стирки.'],
                        ['title' => 'Дождевая и талая вода', 'text' => 'Собираем её и используем для хозяйственных нужд.'],
                        ['title' => 'Сезонная кухня', 'text' => 'Меню строится по сезону, продукты — от местных поставщиков: короткий путь до стола, меньше транспорта.'],
                        ['title' => 'Питание без шведского стола', 'text' => 'Вместо шведского стола — горное сбалансированное питание, рассчитанное на каждого гостя. Мы не готовим лишнего и меньше выбрасываем еды.'],
                    ],
                ]],
                ['type' => 'text', 'data' => [
                    'heading' => 'Гости и горы',
                    'body' => '<p>Мы хотим, чтобы бережное отношение к природе стало привычкой каждого, кто приезжает к нам: рассказываем, как вести себя в горах, и подаём пример сами.</p><p>Горы — наш общий дом. Приглашаем вас быть в нём аккуратным гостем.</p>',
                ]],
            ],
            'en' => [
                ['type' => 'intro', 'data' => [
                    'eyebrow' => 'Our principle',
                    'heading' => 'In the mountains, we are guests',
                    'text' => 'Sustainable development is a founding principle of Tenir: we welcome guests without harming the natural ecosystem. For us, caring for nature is part of the service, not an optional extra.',
                ]],
                ['type' => 'pillars', 'data' => [
                    'eyebrow' => 'The building',
                    'heading' => 'Gentle on the rock and the landscape',
                    'items' => [
                        ['title' => 'Eco materials', 'text' => 'Larch-shingle facades keep the landscape authentic and protect the structure from moisture, sun and wind.'],
                        ['title' => 'No foundations', 'text' => 'Modules sit on a metal base anchored into the rock — no permanent structures and minimal disturbance to the terrain.'],
                        ['title' => 'Warmth without waste', 'text' => 'Multi-layer sandwich panels minimise heat loss and significantly cut electricity used for heating.'],
                        ['title' => 'Energy saving', 'text' => 'Long-lasting, low-consumption LED lighting throughout the modules.'],
                    ],
                ]],
                ['type' => 'pillars', 'data' => [
                    'eyebrow' => 'Every day',
                    'heading' => 'Less waste, more care for resources',
                    'items' => [
                        ['title' => 'Dispensers, not miniature bottles', 'text' => 'Shampoo, shower gel and conditioner come from refillable dispensers in the bathrooms — no single-use plastic bottles.'],
                        ['title' => 'Soap in paper wrapping', 'text' => 'No unnecessary plastic packaging in your room.'],
                        ['title' => 'Towels on request', 'text' => 'We change them when you ask, saving water and laundry detergent.'],
                        ['title' => 'Rain and meltwater', 'text' => 'We collect it and use it for household needs.'],
                        ['title' => 'Seasonal cuisine', 'text' => 'The menu follows the seasons and relies on local suppliers: a short path to the table and less transport.'],
                        ['title' => 'No breakfast buffet', 'text' => 'Instead of a buffet, we serve balanced mountain meals portioned for each guest. We cook no more than needed and throw away less food.'],
                    ],
                ]],
                ['type' => 'text', 'data' => [
                    'heading' => 'Guests and the mountains',
                    'body' => '<p>We want respect for nature to become a habit for everyone who stays with us: we explain how to behave in the mountains and lead by example.</p><p>The mountains are our shared home. We invite you to be a considerate guest in it.</p>',
                ]],
            ],
            'kk' => [
                ['type' => 'intro', 'data' => [
                    'eyebrow' => 'Біздің қағидат',
                    'heading' => 'Тауда біз — қонақпыз',
                    'text' => 'Тұрақты даму — Tenir-дің негізі: біз қонақтарды табиғи экожүйеге зиян келтірмей қабылдаймыз. Табиғатқа қамқорлық — біз үшін қосымша опция емес, сервистің бір бөлігі.',
                ]],
                ['type' => 'pillars', 'data' => [
                    'eyebrow' => 'Ғимарат',
                    'heading' => 'Жартас пен ландшафтқа ұқыпты',
                    'items' => [
                        ['title' => 'Эко-материалдар', 'text' => 'Қарағай тақтайшадан жасалған қасбеттер ландшафттың табиғи көрінісін сақтап, құрылымды ылғалдан, күннен және желден қорғайды.'],
                        ['title' => 'Іргетассыз', 'text' => 'Модульдер жартасқа бекітілген металл негізде тұрады — капиталды құрылыссыз, бедерге ең аз әсермен.'],
                        ['title' => 'Жылу шығынсыз', 'text' => 'Көп қабатты сэндвич-панельдер жылу шығынын азайтып, жылытуға кететін электр қуатын айтарлықтай үнемдейді.'],
                        ['title' => 'Энергия үнемдеу', 'text' => 'Барлық модульде ұзақ қызмет ететін, аз қуат тұтынатын LED жарық.'],
                    ],
                ]],
                ['type' => 'pillars', 'data' => [
                    'eyebrow' => 'Күн сайын',
                    'heading' => 'Қалдық аз, ресурсқа қамқор',
                    'items' => [
                        ['title' => 'Шағын құтының орнына диспенсер', 'text' => 'Жуынатын бөлмелерде шампунь, гель және кондиционер бір реттік пластик құтысыз, қайта толтырылатын диспенсерлерде тұрады.'],
                        ['title' => 'Қағазға оралған сабын', 'text' => 'Бөлмеде артық пластик қаптама жоқ.'],
                        ['title' => 'Орамалдар — қалауыңыз бойынша', 'text' => 'Оларды қонақтың өтініші бойынша ауыстырамыз: бұл су мен жуу құралын үнемдейді.'],
                        ['title' => 'Жаңбыр және еріген қар суы', 'text' => 'Оны жинап, шаруашылық қажеттіліктеріне пайдаланамыз.'],
                        ['title' => 'Маусымдық асхана', 'text' => 'Мәзір маусымға қарай жасалады, өнімдер жергілікті жеткізушілерден: үстелге жол қысқа, тасымал аз.'],
                        ['title' => 'Швед үстелінсіз тамақтану', 'text' => 'Швед үстелінің орнына әр қонаққа есептелген тау жағдайына лайық теңгерімді тамақ. Артық дайындамаймыз, тағам аз тасталады.'],
                    ],
                ]],
                ['type' => 'text', 'data' => [
                    'heading' => 'Қонақтар және таулар',
                    'body' => '<p>Бізге келген әр адам үшін табиғатқа ұқыпты қарау әдетке айналғанын қалаймыз: тауда өзін қалай ұстау керегін айтамыз және өзіміз үлгі көрсетеміз.</p><p>Таулар — біздің ортақ үйіміз. Сізді онда ұқыпты қонақ болуға шақырамыз.</p>',
                ]],
            ],
        ];
    }
}
