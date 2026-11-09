<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Языки сайта
    |--------------------------------------------------------------------------
    | Первый язык — основной (обязательные поля в админке).
    | fallback — откуда брать текст, если перевод не заполнен.
    | exely — код языка для модуля бронирования TravelLine/Exely.
    | hreflang — значение для <link rel="alternate" hreflang>.
    */
    'locales' => [
        'kk' => ['label' => 'QZ', 'name' => 'Қазақша', 'hreflang' => 'kk', 'exely' => 'kk', 'fallback' => 'ru'],
        'ru' => ['label' => 'RU', 'name' => 'Русский', 'hreflang' => 'ru', 'exely' => 'ru', 'fallback' => null],
        'en' => ['label' => 'EN', 'name' => 'English', 'hreflang' => 'en', 'exely' => 'en', 'fallback' => 'ru'],
        'zh' => ['label' => '中文', 'name' => '中文', 'hreflang' => 'zh-Hans', 'exely' => 'zh', 'fallback' => 'en'],
    ],

    // Главный язык сайта: первый в переключателе, x-default и язык «по умолчанию»,
    // если язык браузера не поддерживается.
    'default_locale' => 'kk',

    // Награды из раздела «Награды и СМИ» относятся к этому отелю (слаг) — для schema.org/award.
    'recognition_hotel' => 'shymbulak',

    // Исходный язык контента в админке (обязательные поля). Тексты, не переведённые
    // на казахский, показываются на русском.
    'source_locale' => 'ru',

    // Какой язык показывать посетителю с этим языком браузера (если он не поддерживается напрямую).
    'locale_aliases' => ['be' => 'ru', 'uk' => 'ru', 'ky' => 'kk', 'uz' => 'kk', 'tt' => 'ru', 'zh' => 'zh'],

    /*
    |--------------------------------------------------------------------------
    | Кэш страниц
    |--------------------------------------------------------------------------
    | Готовый HTML публичных страниц хранится в кэше приложения. Сбрасывается
    | автоматически при любом сохранении в админке.
    */
    'gifts' => [
        // Витрина подарочных сертификатов (gifter). Адрес можно переопределить в админке: Настройки сайта → Сертификаты.
        'url' => env('GIFTER_URL', 'https://gifts.tenirhotels.com'),
        'ttl' => (int) env('GIFTER_TTL', 600), // секунд, через сколько обновлять список при показе страницы
    ],

    'page_cache' => [
        'enabled' => env('PAGE_CACHE', true),
        'ttl' => (int) env('PAGE_CACHE_TTL', 21600),
    ],

    /*
    |--------------------------------------------------------------------------
    | Зарезервированные слаги
    |--------------------------------------------------------------------------
    | Нельзя использовать как адрес страницы (/{locale}/{page}) — заняты
    | маршрутами сайта.
    */
    'reserved_slugs' => [
        'destinations', 'experiences', 'offers', 'stories', 'booking',
        'contact', 'search', 'admin', 'api', 'sitemap', 'storage', 'livewire',
        // короткие адреса режима «один отель»
        'rooms', 'dining', 'gallery', 'getting-here',
    ],

    /*
    |--------------------------------------------------------------------------
    | Бронирование — Exely (TravelLine)
    |--------------------------------------------------------------------------
    | Интеграция через JS-модуль TravelLine, как в V1:
    | на странице /{locale}/booking встраивается booking-form.
    | Плашка бронирования на всех страницах — наш компонент, который
    | передаёт параметры в модуль через адрес страницы.
    |
    | ВНИМАНИЕ: названия параметров (params) нужно подтвердить у поддержки
    | Exely. room-type и special-offer подтверждены текущим сайтом Exely.
    */
    'exely' => [
        'context' => env('EXELY_CONTEXT', 'TL-INT-tenirhotels'),
        'loader' => 'https://ibe.tlintegration.com/integration/loader.js',
        // Код отеля в Exely (TravelLine) для виджета отзывов
        'provider' => env('EXELY_PROVIDER', '9305'),
        // Виджет отзывов работает через прежнюю интеграцию (как на tenirhotels.com):
        // другой загрузчик, пространство имён bookingengine и контекст TL-EXPRESS.
        'reviews_loader' => env('EXELY_REVIEWS_LOADER', 'https://kz-ibe.hopenapi.com/integration/loader.js'),
        // Форма поиска на главной — как на tenirhotels.com: профиль TL-EXPRESS.express-3-new-aa (серая тема).
        'search_context' => env('EXELY_SEARCH_CONTEXT', 'TL-EXPRESS.express-3-new-aa'),
        'reviews_context' => env('EXELY_REVIEWS_CONTEXT', 'TL-EXPRESS'),
        'params' => [
            'check_in' => 'date',          // YYYY-MM-DD
            'nights' => 'nights',
            'adults' => 'adults',
            'children' => 'children',      // возраст детей через запятую
            'room_type' => 'room-type',
            'special_offer' => 'special-offer',
            'promo_code' => 'promo-code',
        ],
        'max_adults' => 6,
        'max_children' => 4,
    ],

    /*
    |--------------------------------------------------------------------------
    | Категории впечатлений (по образцу Aman / Six Senses)
    |--------------------------------------------------------------------------
    */
    'experience_categories' => [
        'sky' => ['ru' => 'Небо и звёзды', 'en' => 'Sky & stars', 'kk' => 'Аспан мен жұлдыздар'],
        'active' => ['ru' => 'Активный отдых', 'en' => 'Active adventure', 'kk' => 'Белсенді демалыс'],
        'wellness' => ['ru' => 'Велнес', 'en' => 'Wellness', 'kk' => 'Велнес'],
        'culture' => ['ru' => 'Культура', 'en' => 'Culture', 'kk' => 'Мәдениет'],
        'dining' => ['ru' => 'Гастрономия', 'en' => 'Dining', 'kk' => 'Гастрономия'],
        'celebrations' => ['ru' => 'Праздники и события', 'en' => 'Celebrations & events', 'kk' => 'Мерекелер мен іс-шаралар'],
        'family' => ['ru' => 'Для семьи', 'en' => 'Family', 'kk' => 'Отбасыға'],
    ],

    'seasons' => [
        'winter' => ['ru' => 'Зима', 'en' => 'Winter', 'kk' => 'Қыс'],
        'spring' => ['ru' => 'Весна', 'en' => 'Spring', 'kk' => 'Көктем'],
        'summer' => ['ru' => 'Лето', 'en' => 'Summer', 'kk' => 'Жаз'],
        'autumn' => ['ru' => 'Осень', 'en' => 'Autumn', 'kk' => 'Күз'],
    ],
];
