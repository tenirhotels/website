# Tenir Hotels — сайт сети (V2)

Laravel 12 + Filament 4 (админка) + Blade + Tailwind 4. Бронирование — модуль TravelLine / Exely.

## Быстрый старт

```bash
composer install
npm install && npm run build
cp .env.example .env && php artisan key:generate
php artisan migrate --seed        # контент Tenir Shymbulak, страницы бренда, редиректы
php artisan storage:link
php artisan make:filament-user    # пользователь админки
php artisan serve                 # сайт: /ru, админка: /admin
php artisan test                  # 14 тестов: страницы, SEO, бронирование, редиректы, админка
```

## Адреса

| Адрес | Что |
|---|---|
| `/` | редирект на язык браузера |
| `/{ru,kk,en,zh}` | главная (пока один отель — о нём) |
| `/{l}/destinations` | все отели + «скоро открытие» |
| `/{l}/destinations/{отель}` | обзор отеля; подразделы `rooms`, `rooms/{номер}`, `dining`, `experiences`, `offers`, `gallery`, `getting-here` |
| `/{l}/experiences`, `/{l}/offers`, `/{l}/stories` | разделы всей сети |
| `/{l}/booking` | модуль Exely (noindex) |
| `/{l}/{страница}` | страницы бренда из блоков: `about`, `sustainability`, `privacy`, `terms` |
| `/sitemap.xml`, `/robots.txt` | все языки с hreflang |

## Контент (админка `/admin`)

- **Отели**: отели, номера, рестораны, частые вопросы.
- **Контент**: спецпредложения, впечатления, истории, страницы бренда (конструктор блоков: вступление, текст, фото+текст, цитата, «Тәңір», принципы, цифры, галерея, призыв).
- **Сайт**: настройки (контакты, соцсети, аналитика, SEO по умолчанию), редиректы со счётчиком переходов.

Переводимые поля хранятся в JSON (`{"ru": …, "kk": …}`) и редактируются вкладками языков. Пустой перевод берётся по цепочке: `kk → ru`, `en → ru`, `zh → en → ru` (`config/tenir.php`). Тексты интерфейса — `lang/{ru,kk,en,zh}/site.php` (kk и zh нужно вычитать).

## Бронирование (Exely / TravelLine)

- Контекст модуля: `EXELY_CONTEXT` (по умолчанию `TL-INT-tenirhotels`, как в V1); у отеля можно задать свой.
- Плашка бронирования (шапка, герой, окно, нижняя панель на телефоне) отправляет `check_in`, `check_out`, `adults`, `children`, `promo_code` на `/{l}/booking`. `BookingController` переводит их в параметры модуля и редиректит — модуль читает их из адреса.
- Кнопки номеров и предложений ведут сразу на тариф: `room-type` / `special-offer` (ID задаются в админке).
- **Проверить у Exely**: названия параметров `date`, `nights`, `adults`, `children`, `promo-code` (`config/tenir.php → exely.params`).

## Редиректы

`RedirectsSeeder` — 301 со старых адресов V1 (`/hotel`, `/about`, `/activities`, `/news`) и конструктора Exely (`/ru-ru/…`, `/en-gb/…`, `/kk-kz/…`, `/zh-cn/…`). Параметры запроса сохраняются. Редактируются в админке.

## Оформление

Палитра и шрифты — только в `resources/css/app.css` (`@theme`). Шрифт Gill Sans Pro Cyrillic перенесён из V1 — проверьте веб-лицензию. Фото в сидах — из V1, замените в админке.

## Продакшен

VPS (PHP 8.3, Nginx, MySQL/PostgreSQL) за Cloudflare. В `.env`: `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL`, БД, `ADMIN_EMAILS` (кому открыта админка). После деплоя: `php artisan optimize && php artisan filament:optimize`.

## Что дальше

- Подписка на новости → Bitrix24 / сервис рассылок (форма на главной пока без отправки).
- Виджет отзывов Exely (поле `reviews_widget` у отеля).
- Перевести контент на казахский и китайский.
