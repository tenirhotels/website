<?php

namespace App\Filament\Pages;

use App\Filament\Support\Fields;
use App\Models\Setting;
use BackedEnum;
use Filament\Actions\Action;
use App\Support\Property;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use App\Support\PageCache;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/**
 * Общие настройки сайта: бренд, контакты, соцсети, аналитика, SEO по умолчанию.
 * Хранятся в таблице settings группами: brand, contacts, social, analytics, seo.
 */
class SiteSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|UnitEnum|null $navigationGroup = 'Сайт';

    protected static ?string $navigationLabel = 'Настройки';

    protected static ?string $title = 'Настройки сайта';

    protected static ?int $navigationSort = 0;

    private const GROUPS = ['site', 'brand', 'contacts', 'social', 'analytics', 'perf', 'seo', 'cable'];

    public ?array $data = [];

    public function mount(): void
    {
        $values = [];
        foreach (self::GROUPS as $group) {
            $values[$group] = Setting::get($group, []);
        }
        $this->form->fill($values);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->statePath('data')->components([
            Section::make('Режим сайта')
                ->description(fn () => sprintf(
                    'Сейчас: %s (опубликовано отелей: %d).',
                    Property::isSingle() ? 'один отель — короткие адреса /rooms, /dining' : 'сеть — /destinations/{отель}/…',
                    Property::publishedCount(),
                ))
                ->schema([
                    Select::make('site.mode')->label('Режим')->default('auto')->selectablePlaceholder(false)->options([
                        'auto' => 'Автоматически: один опубликованный отель — сайт отеля, два и больше — сеть',
                        'single' => 'Всегда один отель (главный отель — «Показывать на главной»)',
                        'multi' => 'Всегда сеть',
                    ])->helperText('При смене режима адреса другого режима отдают 301 на актуальные.'),
                ]),
            Section::make('Бренд')->schema([
                Fields::translatable(fn (string $l) => [
                    TextInput::make("brand.name.$l")->label('Название бренда')->placeholder('Tenir Hotels'),
                    TextInput::make("brand.tagline.$l")->label('Слоган бренда')->placeholder('Back to Nature'),
                    Textarea::make("brand.footer_text.$l")->label('Текст в футере')->rows(2),
                    TextInput::make("brand.newsletter_heading.$l")->label('Заголовок подписки')->placeholder('Новые места Tenir'),
                ], 'brand'),
                TextInput::make('brand.legal_name')->label('Юр. лицо')->placeholder('ТОО «Alpine Hotel»'),
            ]),
            Section::make('Контакты отдела бронирования')->columns(2)->schema([
                TextInput::make('contacts.phone')->label('Телефон')->tel(),
                TextInput::make('contacts.whatsapp')->label('WhatsApp')->tel(),
                TextInput::make('contacts.email')->label('E-mail')->email(),
                TextInput::make('contacts.sales_phone')->label('Телефон: корпоративные запросы, туроператоры')->tel(),
                TextInput::make('contacts.sales_email')->label('E-mail: корпоративные запросы, туроператоры')->email(),
                TextInput::make('contacts.hours')->label('Часы работы')->placeholder('09:00–21:00'),
            ]),
            Section::make('Подарочные сертификаты')->description('Список и цены берутся из gifter (gifts.tenirhotels.com), заказ и оплата — на его странице.')->columns(2)->schema([
                Toggle::make('gifts.enabled')->label('Показывать сертификаты на сайте')->default(true),
                TextInput::make('gifts.url')->label('Адрес витрины gifter')->url()->placeholder('https://gifts.tenirhotels.com'),
            ]),
            Section::make('Канатная дорога (страница «Как добраться»)')
                ->description('Расписание задаёт курорт (shymbulak.com/info). Пустые поля — значения по умолчанию; меняйте при смене сезона.')
                ->columns(2)
                ->schema([
                    TextInput::make('cable.hours_medeu')->label('Медеу — Шымбулак')->placeholder('09:00–18:00'),
                    TextInput::make('cable.hours_combi1')->label('Комби-1')->placeholder('09:00–17:00'),
                    TextInput::make('cable.hours_combi2')->label('Комби-2')->placeholder('09:00–16:30'),
                    TextInput::make('cable.hours_note')->label('Пояснение (сезон, дни)')->placeholder('ежедневно, будни и выходные'),
                    TextInput::make('cable.guest_from')->label('Подъём для гостей отеля: с')->default('18:00')->placeholder('18:00'),
                    TextInput::make('cable.guest_to')->label('Подъём для гостей отеля: до')->default('19:00')->placeholder('19:00'),
                ]),
            Section::make('Соцсети')->columns(2)->schema([
                TextInput::make('social.instagram')->label('Instagram')->url(),
                TextInput::make('social.youtube')->label('YouTube')->url(),
                TextInput::make('social.tiktok')->label('TikTok')->url(),
                TextInput::make('social.soundcloud')->label('SoundCloud')->url(),
                TextInput::make('social.facebook')->label('Facebook')->url(),
            ]),
            Section::make('Аналитика и пиксели')
                ->description('Счётчики подключаются после первого действия посетителя (скролл, касание) или через 4 секунды — так они не замедляют загрузку страницы. Пустое поле — счётчик не выводится.')
                ->columns(2)
                ->schema([
                    TextInput::make('analytics.ga4')->label('Google Analytics 4')->placeholder('G-XXXXXXXXXX')->helperText('Идентификатор потока данных GA4.'),
                    TextInput::make('analytics.gtm')->label('Google Tag Manager')->placeholder('GTM-XXXXXXX'),
                    TextInput::make('analytics.yandex_metrika')->label('Яндекс Метрика')->placeholder('12345678')->helperText('Только номер счётчика.'),
                    Toggle::make('analytics.yandex_webvisor')->label('Вебвизор Яндекс Метрики')->helperText('Записи сессий. Заметно нагружает страницу — включайте только при необходимости.')->inline(false),
                    TextInput::make('analytics.meta_pixel')->label('Meta Pixel (Facebook / Instagram)')->placeholder('1234567890123456')->helperText('ID пикселя из Meta Events Manager.'),
                    Toggle::make('analytics.load_immediately')->label('Загружать счётчики сразу')->helperText('Точнее считает мгновенные отказы, но снижает оценку PageSpeed.')->inline(false),
                    TextInput::make('analytics.verify_google')->label('Google Search Console — код подтверждения')->helperText('Значение content из мета-тега google-site-verification.'),
                    TextInput::make('analytics.verify_yandex')->label('Яндекс Вебмастер — код подтверждения')->helperText('Значение content из мета-тега yandex-verification.'),
                    TextInput::make('analytics.verify_meta')->label('Meta — подтверждение домена')->helperText('Значение content из facebook-domain-verification.'),
                    Textarea::make('analytics.custom_head')->label('Свой код в <head>')->rows(4)->columnSpanFull()->helperText('Любой HTML/скрипт целиком (например, пиксель TikTok). Выводится как есть — вставляйте только проверенный код.'),
                    Textarea::make('analytics.custom_body')->label('Свой код перед </body>')->rows(4)->columnSpanFull(),
                ]),
            Section::make('Скорость сайта')
                ->description(fn () => 'Кэш готовых страниц: '.(PageCache::enabled() ? 'включён' : 'выключен').'. Кэш сбрасывается сам при любом сохранении в админке.')
                ->schema([
                    Toggle::make('perf.page_cache')->label('Кэшировать страницы для посетителей')->default(true)
                        ->helperText('Страницы отдаются мгновенно, без обращения к базе данных. Отключайте только на время отладки.'),
                ])
                ->footerActions([
                    Action::make('flushCache')->label('Очистить кэш страниц сейчас')->color('gray')->action(function () {
                        PageCache::flush();
                        Notification::make()->title('Кэш страниц очищен')->success()->send();
                    }),
                ]),
            Section::make('SEO по умолчанию')->schema([
                Fields::translatable(fn (string $l) => [
                    TextInput::make("seo.default_title.$l")->label('Title главной')->maxLength(70),
                    Textarea::make("seo.default_description.$l")->label('Description главной')->rows(2)->maxLength(170),
                    TextInput::make("seo.default_keywords.$l")->label('Ключевые слова по умолчанию')->maxLength(250),
                ], 'seo-default'),
                Fields::image('seo.og_image', 'Картинка для соцсетей по умолчанию', 'og'),
            ]),
        ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('save')
                ->footer([
                    Actions::make([
                        Action::make('save')->label('Сохранить')->submit('save')->keyBindings(['mod+s']),
                    ]),
                ]),
        ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        foreach (self::GROUPS as $group) {
            Setting::put($group, $data[$group] ?? []);
        }

        Notification::make()->title('Сохранено')->success()->send();
    }
}
