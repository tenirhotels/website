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

    private const GROUPS = ['site', 'brand', 'contacts', 'social', 'analytics', 'seo'];

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
                    TextInput::make("brand.tagline.$l")->label('Слоган бренда')->placeholder('Ближе к небу'),
                    Textarea::make("brand.footer_text.$l")->label('Текст в футере')->rows(2),
                    TextInput::make("brand.newsletter_heading.$l")->label('Заголовок подписки')->placeholder('Новые места Tenir'),
                ], 'brand'),
                TextInput::make('brand.legal_name')->label('Юр. лицо')->placeholder('ТОО «Alpine Hotel»'),
            ]),
            Section::make('Контакты отдела бронирования')->columns(2)->schema([
                TextInput::make('contacts.phone')->label('Телефон')->tel(),
                TextInput::make('contacts.whatsapp')->label('WhatsApp')->tel(),
                TextInput::make('contacts.email')->label('E-mail')->email(),
                TextInput::make('contacts.hours')->label('Часы работы')->placeholder('09:00–21:00'),
            ]),
            Section::make('Соцсети')->columns(3)->schema([
                TextInput::make('social.instagram')->label('Instagram')->url(),
                TextInput::make('social.facebook')->label('Facebook')->url(),
                TextInput::make('social.youtube')->label('YouTube')->url(),
            ]),
            Section::make('Аналитика')->columns(2)->schema([
                TextInput::make('analytics.ga4')->label('Google Analytics 4 (G-…)'),
                TextInput::make('analytics.yandex_metrika')->label('Яндекс Метрика (номер счётчика)'),
                TextInput::make('analytics.gtm')->label('Google Tag Manager (GTM-…)'),
            ]),
            Section::make('SEO по умолчанию')->schema([
                Fields::translatable(fn (string $l) => [
                    TextInput::make("seo.default_title.$l")->label('Title главной')->maxLength(70),
                    Textarea::make("seo.default_description.$l")->label('Description главной')->rows(2)->maxLength(170),
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
