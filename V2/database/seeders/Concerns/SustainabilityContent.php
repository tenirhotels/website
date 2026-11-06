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
                ['type' => 'text', 'data' => [
                    'heading' => 'Меньше отходов — с первого утра',
                    'body' => '<p>Мы начинаем с мелочей, из которых складывается след отеля. В ванных комнатах шампунь, гель и кондиционер ждут вас в диспенсерах с рефиллами — без одноразовых пластиковых флаконов, которые обычно уезжают в мусор после одной ночи. Мыло приходит в бумажной обёртке, а полотенца мы меняем по вашей просьбе: так мы бережём воду и меньше нагружаем стирку.</p>',
                ]],
                ['type' => 'text', 'data' => [
                    'heading' => 'Вода и вкус гор',
                    'body' => '<p>Дождевую и талую воду мы собираем и используем для хозяйственных нужд — горы щедро делятся ею сами. На кухне мы живём в ритме сезонов: меню меняется вместе с природой, а продукты приезжают от местных поставщиков, пройдя короткий путь до вашего стола.</p><p>Поэтому у нас нет шведского стола. Мы готовим горное сбалансированное питание на каждого гостя — ровно столько, сколько нужно, чтобы вкусно и сытно начать день и не выбросить лишнего.</p>',
                ]],
                ['type' => 'quote', 'data' => [
                    'quote' => 'Горы — наш общий дом. Мы рассказываем гостям, как вести себя в нём, и подаём пример сами: бережное отношение к природе становится привычкой.',
                    'caption' => 'Tenir Hotels',
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
                ['type' => 'text', 'data' => [
                    'heading' => 'Less waste, from the first morning',
                    'body' => '<p>We begin with the small things that add up to a hotel’s footprint. In the bathrooms, shampoo, shower gel and conditioner wait for you in refillable dispensers — no single-use plastic bottles that usually end up in the bin after one night. Soap arrives in paper wrapping, and towels are changed at your request, so we save water and ease the load on the laundry.</p>',
                ]],
                ['type' => 'text', 'data' => [
                    'heading' => 'Water and the taste of the mountains',
                    'body' => '<p>We collect rain and meltwater and use it for household needs — the mountains share it generously. In the kitchen we live by the rhythm of the seasons: the menu changes with nature, and ingredients come from local suppliers, travelling only a short way to your table.</p><p>That is why there is no breakfast buffet. We prepare balanced mountain meals for each guest — just enough to start the day well, with nothing wasted.</p>',
                ]],
                ['type' => 'quote', 'data' => [
                    'quote' => 'The mountains are our shared home. We show our guests how to treat it with care, and lead by example, until respect for nature becomes a habit.',
                    'caption' => 'Tenir Hotels',
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
                ['type' => 'text', 'data' => [
                    'heading' => 'Алғашқы таңнан бастап қалдық аз',
                    'body' => '<p>Біз қонақүйдің ізін құрайтын ұсақ нәрселерден бастаймыз. Жуынатын бөлмелерде шампунь, гель және кондиционер қайта толтырылатын диспенсерлерде тұрады — бір түннен кейін қоқысқа кететін бір реттік пластик құтылар жоқ. Сабын қағазға оралып беріледі, ал орамалдарды сіздің өтінішіңіз бойынша ауыстырамыз: осылайша суды үнемдеп, кірді аз жуамыз.</p>',
                ]],
                ['type' => 'text', 'data' => [
                    'heading' => 'Су және таудың дәмі',
                    'body' => '<p>Жаңбыр мен еріген қар суын жинап, шаруашылық қажеттіліктеріне пайдаланамыз — таулар оны жомарттықпен бөліседі. Асханада біз маусым ырғағымен өмір сүреміз: мәзір табиғатпен бірге өзгереді, ал өнімдер жергілікті жеткізушілерден үстеліңізге қысқа жолмен келеді.</p><p>Сондықтан бізде швед үстелі жоқ. Біз әр қонаққа таулық теңгерімді тамақ дайындаймыз — күнді дәмді бастауға жеткілікті мөлшерде, артық тастамай.</p>',
                ]],
                ['type' => 'quote', 'data' => [
                    'quote' => 'Таулар — біздің ортақ үйіміз. Қонақтарға онда өзін қалай ұстау керегін айтып, өзіміз үлгі боламыз: табиғатқа ұқыпты қарау әдетке айналады.',
                    'caption' => 'Tenir Hotels',
                ]],
            ],
        ];
    }
}
