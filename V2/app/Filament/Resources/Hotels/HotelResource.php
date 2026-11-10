<?php

namespace App\Filament\Resources\Hotels;

use App\Filament\Resources\Hotels\Pages\CreateHotel;
use App\Filament\Resources\Hotels\Pages\EditHotel;
use App\Filament\Resources\Hotels\Pages\ListHotels;
use App\Filament\Support\Fields;
use App\Models\Hotel;
use App\Support\Property;
use BackedEnum;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class HotelResource extends Resource
{
    use \App\Filament\Support\UsesIdInUrls;

    // В админке адреса записей по id, на сайте — по слагу.
    protected static ?string $recordRouteKeyName = 'id';

    protected static ?string $model = Hotel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static string|UnitEnum|null $navigationGroup = 'Отели';

    protected static ?string $modelLabel = 'отель';

    protected static ?string $pluralModelLabel = 'Отели';

    public static function getPluralModelLabel(): string
    {
        return Property::isSingle() ? 'Отель' : 'Отели';
    }

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('hotel')->columnSpanFull()->persistTabInQueryString()->tabs([
                Tab::make('Основное')->schema([
                    Fields::translatable(fn (string $l, bool $d) => [
                        Fields::titleInput('name', $l, $d, 'Название отеля'),
                        TextInput::make("tagline.$l")->label('Слоган')->placeholder('Ближе к небу'),
                        Textarea::make("summary.$l")->label('Краткое описание')->rows(3)
                            ->helperText('Для карточек, превью и SEO, 1–2 предложения.'),
                        RichEditor::make("description.$l")->label('Описание'),
                    ]),
                    Fields::twoColumns([
                        Fields::slug()->prefix('/destinations/'),
                        Select::make('status')->label('Статус')->required()->live()->options([
                            Hotel::STATUS_OPEN => 'Открыт',
                            Hotel::STATUS_COMING_SOON => 'Скоро открытие',
                        ])->default(Hotel::STATUS_OPEN),
                    ]),
                    Fields::translatable(fn (string $l) => [
                        TextInput::make("opening_label.$l")->label('Подпись открытия')->placeholder('Открытие в 2028'),
                    ], 'opening')->visible(fn (Get $get) => $get('status') === Hotel::STATUS_COMING_SOON),
                ]),

                Tab::make('Фото и видео')->schema([
                    Fields::image('hero_image', 'Главное фото (герой) — зима, с ноября по апрель', 'hotels'),
                    Fields::image('hero_image_summer', 'Главное фото — лето, с мая по октябрь (необязательно)', 'hotels')
                        ->helperText('Если не загружено, круглый год показывается основное фото.'),
                    Fields::image('intro_image', 'Фото в блоке «О отеле» на главной (вертикальное, 5:6)', 'hotels')
                        ->helperText('Большое фото справа от текста «Тишина гор, звёзды и снег…». Если не загружено, берётся первое фото галереи, а если и его нет — главное фото.'),
                    \Filament\Forms\Components\FileUpload::make('hero_video')
                        ->label('Видео для героя (mp4, только компьютер)')
                        ->helperText('10–15 секунд, 1920×1080, до 15 МБ. Показывается только на компьютере поверх главного фото; на телефоне остаётся фото. Начинается без звука; если в файле есть звуковая дорожка, в углу появится кнопка «Включить звук». Нет звука в файле — кнопки не будет. Чтобы убрать видео — удалите файл.')
                        ->disk('public')->directory('hotels/video')
                        ->acceptedFileTypes(['video/mp4'])->maxSize(15360),
                    Fields::gallery('gallery', 'hotels/gallery'),
                ]),

                Tab::make('Цифры')->schema([
                    Repeater::make('highlights')->label('Ключевые цифры (на главной и странице отеля)')
                        ->schema([
                            TextInput::make('value')->label('Значение')->placeholder('3 200 м')->required(),
                            Fields::translatable(fn (string $l, bool $d) => [
                                TextInput::make("label.$l")->label('Подпись')->placeholder('над уровнем моря')->required($d),
                            ], 'hl'),
                        ])
                        ->maxItems(4)->reorderable()->collapsible()
                        ->itemLabel(fn (array $state) => $state['value'] ?? null),
                ]),

                Tab::make('Локация и контакты')->schema([
                    Fields::translatable(fn (string $l) => [
                        TextInput::make("region.$l")->label('Регион')->placeholder('Заилийский Алатау'),
                        TextInput::make("city.$l")->label('Город')->placeholder('Алматы'),
                        TextInput::make("address.$l")->label('Адрес'),
                        RichEditor::make("getting_here.$l")->label('Как добраться'),
                    ], 'location'),
                    Fields::twoColumns([
                        TextInput::make('altitude_m')->label('Высота, м')->numeric(),
                        TextInput::make('country_code')->label('Страна (ISO)')->default('KZ')->maxLength(2),
                        TextInput::make('latitude')->label('Широта')->numeric(),
                        TextInput::make('longitude')->label('Долгота')->numeric(),
                        TextInput::make('phone')->label('Телефон')->tel(),
                        TextInput::make('whatsapp')->label('WhatsApp')->tel(),
                        TextInput::make('email')->label('E-mail')->email(),
                        TextInput::make('check_in_time')->label('Заезд с')->placeholder('14:00'),
                        TextInput::make('check_out_time')->label('Выезд до')->placeholder('12:00'),
                    ]),
                ]),

                Tab::make('Канатная дорога')->schema([
                    \Filament\Forms\Components\Toggle::make('cable.enabled')->label('К отелю ведёт канатная дорога курорта Шымбулак')->live()
                        ->helperText('Включите только для отеля на курорте. Тогда на странице «Как добраться» появятся линии M-1/K-1/K-2, часы работы, калькулятор и панорамы. У отелей в других местах оставьте выключенным.'),
                    Section::make('Расписание и фото')
                        ->description('Расписание задаёт курорт (shymbulak.com/info). Пустые поля — значения по умолчанию; меняйте при смене сезона.')
                        ->columns(2)
                        ->visible(fn (Get $get) => (bool) $get('cable.enabled'))
                        ->schema([
                            TextInput::make('cable.hours_medeu')->label('M-1 (Медеу — Шымбулак)')->placeholder('09:00–18:00'),
                            TextInput::make('cable.hours_combi1')->label('K-1 (Шымбулак — Средняя)')->placeholder('09:00–17:00'),
                            TextInput::make('cable.hours_combi2')->label('K-2 (Средняя — Верхняя)')->placeholder('09:00–16:30'),
                            TextInput::make('cable.hours_note')->label('Пояснение (сезон, дни)')->placeholder('ежедневно, будни и выходные'),
                            TextInput::make('cable.guest_from')->label('Подъём для гостей отеля: с')->placeholder('18:00'),
                            TextInput::make('cable.guest_to')->label('Подъём для гостей отеля: до')->placeholder('18:30'),
                            Fields::image('cable.photo_medeu', 'Фото станции Медеу', 'cable')->helperText('Показывается справа в карточке «Канатная дорога» на странице «Как добраться»; по нажатию открывается крупно.'),
                            Fields::image('cable.photo_shymbulak', 'Фото станции Шымбулак', 'cable'),
                            Fields::image('cable.photo_combi1', 'Фото Средней станции', 'cable'),
                        ]),
                ]),

                Tab::make('Бронирование')->schema([
                    Section::make('Exely / TravelLine')->schema([
                        TextInput::make('exely_context')->label('Контекст модуля')
                            ->placeholder(config('tenir.exely.context'))
                            ->helperText('Пусто — используется общий контекст сети из настроек.'),
                    ]),
                ]),

                Tab::make('SEO и публикация')->schema([
                    Fields::seo(),
                    Fields::publishing(featured: true)->description(fn () => Property::isSingle()
                        ? 'Сейчас сайт в режиме одного отеля. Опубликуйте второй отель — сайт перейдёт в режим сети (/destinations/…), старые адреса получат 301.'
                        : 'Сайт в режиме сети. «Показывать на главной» — главный отель: на него ведут короткие старые адреса.'),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return Fields::table($table, [
            Fields::column('name', 'Отель'),
            TextColumn::make('slug')->label('Адрес')->getStateUsing(fn ($record) => parse_url($record->url('ru'), PHP_URL_PATH) ?: '/'),
            TextColumn::make('status')->label('Статус')->badge()
                ->formatStateUsing(fn (string $state) => $state === Hotel::STATUS_OPEN ? 'Открыт' : 'Скоро')
                ->color(fn (string $state) => $state === Hotel::STATUS_OPEN ? 'success' : 'warning'),
            TextColumn::make('rooms_count')->label('Номера')->counts('rooms'),
            Fields::publishedColumn(),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\RoomsRelationManager::class,
            RelationManagers\VenuesRelationManager::class,
            RelationManagers\OffersRelationManager::class,
            RelationManagers\ExperiencesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHotels::route('/'),
            'create' => CreateHotel::route('/create'),
            'edit' => EditHotel::route('/{record}/edit'),
        ];
    }
}
