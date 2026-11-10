<?php

namespace App\Filament\Resources\Locations;

use App\Filament\Resources\Locations\Pages\CreateLocation;
use App\Filament\Resources\Locations\Pages\EditLocation;
use App\Filament\Resources\Locations\Pages\ListLocations;
use App\Filament\Support\Fields;
use App\Models\Location;
use BackedEnum;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ViewField;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

/** Локация: сначала создаётся место со всеми его настройками, затем к нему привязываются отели. */
class LocationResource extends Resource
{
    use \App\Filament\Support\UsesIdInUrls;

    protected static ?string $recordRouteKeyName = 'id';

    protected static ?string $model = Location::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    protected static string|UnitEnum|null $navigationGroup = 'Отели';

    protected static ?string $modelLabel = 'локация';

    protected static ?string $pluralModelLabel = 'Локации';

    protected static ?int $navigationSort = 0;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('location')->columnSpanFull()->persistTabInQueryString()->tabs([
                Tab::make('Основное')->schema([
                    Fields::translatable(fn (string $l, bool $d) => [
                        Fields::titleInput('name', $l, $d, 'Название локации'),
                        TextInput::make("region.$l")->label('Регион')->placeholder('Заилийский Алатау'),
                        TextInput::make("city.$l")->label('Город')->placeholder('Алматы'),
                        TextInput::make("address.$l")->label('Адрес'),
                    ]),
                    Fields::twoColumns([
                        Fields::slug(),
                        TextInput::make('country_code')->label('Страна (ISO)')->default('KZ')->maxLength(2)->required(),
                        TextInput::make('altitude_m')->label('Высота, м')->numeric(),
                        TextInput::make('latitude')->label('Широта')->numeric(),
                        TextInput::make('longitude')->label('Долгота')->numeric(),
                        TextInput::make('sort')->label('Порядок')->numeric()->default(0),
                    ]),
                ]),
                Tab::make('Как добраться')->schema([
                    Fields::translatable(fn (string $l) => [
                        RichEditor::make("getting_here.$l")->label('Текст «Как добраться»')
                            ->helperText('Общий для всех отелей этой локации. Линии канатной дороги, трансфер и такси добавляются во вкладке «Транспорт».'),
                    ], 'getting'),
                ]),
                Tab::make('Перед приездом')->schema([
                    Fields::translatable(fn (string $l) => [
                        RichEditor::make("arrival_info.$l")->label('Текст «Что важно знать»')
                            ->helperText('Страница «Перед приездом»: канатная дорога и билеты, багаж, одежда. Общий для всех отелей этой локации. Вопросы и ответы под ним — раздел «Частые вопросы» отеля.'),
                    ], 'arrival'),
                    Section::make('От города до вершины')->description('Уровни высоты для наглядности: от города до отеля. Показываются на странице «Перед приездом» линией профиля. Порядок по высоте: первый — город, второй — нижняя станция канатки, последний — отель.')->schema([
                        Repeater::make('levels')->hiddenLabel()->addActionLabel('Добавить уровень')->collapsed()->cloneable()
                            ->itemLabel(fn (array $state) => trim(($state['name']['ru'] ?? '').' · '.($state['altitude_m'] ?? '').' м', ' ·'))
                            ->schema([
                                TextInput::make('altitude_m')->label('Высота, м')->numeric()->required(),
                                self::translated('name', 'Название', 'lv-name'),
                            ]),
                    ]),
                ]),
                Tab::make('Карта')->schema(self::mapFields()),
            ]),
        ]);
    }

    private static function translated(string $path, string $label, string $id): \Filament\Schemas\Components\Tabs
    {
        return Fields::translatable(fn (string $l) => [TextInput::make("$path.$l")->label($label)], $id);
    }

    /** Кнопка «Указать на карте»: нажатие на иллюстрацию заполняет соседние поля x и y. */
    private static function mapPoint(): ViewField
    {
        return ViewField::make('pick')->hiddenLabel()->dehydrated(false)->columnSpanFull()
            ->view('filament.forms.map-point')
            ->viewData(function (?Location $record): array {
                $map = (array) ($record?->map ?? []);
                $path = $map['images']['winter'] ?? $map['images']['summer'] ?? null;

                return [
                    'image' => $path ? (str_starts_with($path, '/') || str_starts_with($path, 'http') ? $path : media($path)) : null,
                    'width' => (int) ($map['width'] ?? 1667),
                    'height' => (int) ($map['height'] ?? 1179),
                    'others' => collect($map['pins'] ?? [])->map(fn ($p) => [
                        'x' => (int) ($p['x'] ?? 0), 'y' => (int) ($p['y'] ?? 0), 'label' => $p['label']['ru'] ?? ($p['id'] ?? ''),
                    ])->all(),
                ];
            });
    }

    /** Карта локации: иллюстрация, станции, метки, GPS-привязка, подписи времени. */
    private static function mapFields(): array
    {
        $image = fn (string $season, string $label) => Fields::image("map.images.$season", $label, 'locations/map')
            ->afterStateUpdated(fn (Set $set) => $set("map.images.{$season}_sm", null))
            ->helperText('Лучше всего — иллюстрация шириной 1600–2400 px.');

        return [
            Toggle::make('map.enabled')->label('Показывать карту локации на сайте')->default(true)
                ->helperText('Страница «Карта курорта» и блок маршрута в «Как добраться» берут данные только отсюда. Нет карты — их нет на сайте.'),
            Section::make('Иллюстрации')->columns(2)->collapsible()->schema([
                $image('winter', 'Карта — зима'),
                $image('summer', 'Карта — лето'),
                Hidden::make('map.images.winter_sm'),
                Hidden::make('map.images.summer_sm'),
                Fields::twoColumns([
                    TextInput::make('map.width')->label('Ширина картинки, px')->numeric()->default(1667)
                        ->helperText('Координаты меток считаются в пикселях этой картинки.'),
                    TextInput::make('map.height')->label('Высота картинки, px')->numeric()->default(1179),
                    TextInput::make('map.one_way_minutes')->label('В одну сторону всего, мин')->numeric()
                        ->helperText('С пересадками. Показывается на карте, в «Как добраться» и в калькуляторе.'),
                    TextInput::make('map.hotel_panorama')->label('Панорама отеля (папка)')
                        ->helperText('Папка в public/panoramas/ с гранями l, f, r, b, u, d (.jpg). Файлы панорам загружаются на сервер вручную.'),
                ])->columnSpanFull(),
            ]),
            Section::make('Станции (по порядку подъёма)')->collapsible()->collapsed()->schema([
                Repeater::make('map.stations')->hiddenLabel()->addActionLabel('Добавить станцию')->collapsed()->cloneable()
                    ->itemLabel(fn (array $state) => trim(($state['lift'] ?? '').' · '.($state['name']['ru'] ?? $state['key'] ?? ''), ' ·'))
                    ->schema([
                        Fields::twoColumns([
                            TextInput::make('key')->label('Ключ станции (папка панорамы)')->required()->alphaDash()
                                ->helperText('Латиницей, например medeu. Метка станции на карте — с id st-ключ.'),
                            TextInput::make('lift')->label('Линия от этой станции')->placeholder('M-1')
                                ->helperText('Код линии, которая едет ОТ этой станции. У последней станции пусто. Должен совпадать с кодом в «Транспорте».'),
                            TextInput::make('next')->label('До следующей станции, мин')->numeric(),
                        ]),
                        self::translated('name', 'Название станции', 'st-name'),
                        self::translated('tag', 'Короткая подпись (на карте)', 'st-tag'),
                    ]),
            ]),
            Section::make('Метки на карте')->description('Рестораны, активности, станции, отель, направления. x и y — пиксели на иллюстрации.')->collapsible()->collapsed()->schema([
                Repeater::make('map.pins')->hiddenLabel()->addActionLabel('Добавить метку')->collapsed()->cloneable()
                    ->itemLabel(fn (array $state) => trim(($state['n'] ?? '').' '.($state['label']['ru'] ?? $state['id'] ?? '')))
                    ->schema([
                        Fields::twoColumns([
                            TextInput::make('id')->label('Идентификатор')->required()->alphaDash()
                                ->helperText('Уникальный: r1, st-medeu, hotel…'),
                            Select::make('kind')->label('Тип')->required()->options([
                                'restaurant' => 'Ресторан или бар', 'activity' => 'Активный отдых', 'station' => 'Станция',
                                'hotel' => 'Отель', 'place' => 'Место', 'trail' => 'Направление',
                            ]),
                            TextInput::make('n')->label('Номер в списке')->numeric()->helperText('Для ресторанов и активностей.'),
                            TextInput::make('x')->label('x, px')->numeric()->required()->helperText('Проще — кнопкой «Указать на карте» ниже.'),
                            TextInput::make('y')->label('y, px')->numeric()->required(),
                            TextInput::make('cable')->label('Ключ станции')->helperText('Для меток станций: покажет времена «вверх / вниз».'),
                        ]),
                        self::mapPoint(),
                        CheckboxList::make('seasons')->label('Сезон')->options(['winter' => 'Зима', 'summer' => 'Лето'])->columns(2)
                            ->helperText('Пусто — круглый год.'),
                        Toggle::make('detail')->label('Показывать только при сильном приближении'),
                        self::translated('label', 'Название (для отеля берётся название отеля)', 'pin-label'),
                    ]),
            ]),
            Section::make('Подписи поверх карты')->description('Время в пути у стрелок и под шкалой, «В одну сторону…».')->collapsible()->collapsed()->schema([
                Repeater::make('map.overlay')->hiddenLabel()->addActionLabel('Добавить подпись')->collapsed()->cloneable()
                    ->itemLabel(fn (array $state) => ($state['kind'] ?? '').' '.($state['station'] ?? ''))
                    ->schema([
                        Fields::twoColumns([
                            Select::make('kind')->label('Что показать')->required()->live()->options([
                                'total' => 'Всего в одну сторону', 'name' => 'Название станции', 'time' => 'Минуты до следующей станции',
                                'arrow_time' => 'Минуты вдоль стрелки (с поворотом)', 'arrow' => 'Свой текст вдоль стрелки', 'text' => 'Свой текст',
                            ]),
                            TextInput::make('station')->label('Ключ станции')->helperText('Ключ из раздела «Станции», например medeu.')
                                ->visible(fn (Get $get) => in_array($get('kind'), ['name', 'time', 'arrow_time'], true)),
                            TextInput::make('x')->label('x, px')->numeric()->required()->helperText('Проще — кнопкой «Указать на карте» ниже.'),
                            TextInput::make('y')->label('y, px')->numeric()->required(),
                            TextInput::make('rot')->label('Поворот, градусы')->numeric()->default(0)
                                ->visible(fn (Get $get) => in_array($get('kind'), ['arrow', 'arrow_time'], true)),
                        ]),
                        self::mapPoint(),
                        self::translated('text', 'Свой текст', 'ov-text')->visible(fn (Get $get) => in_array($get('kind'), ['arrow', 'text'], true)),
                    ]),
            ]),
            Section::make('Привязка к GPS («Вы здесь»)')->description('Карта схематичная, поэтому чем больше точек — тем точнее. Нужно не меньше двух.')->collapsible()->collapsed()->schema([
                Repeater::make('map.geo_points')->hiddenLabel()->addActionLabel('Добавить точку')->collapsed()->cloneable()
                    ->itemLabel(fn (array $state) => ($state['lat'] ?? '').', '.($state['lng'] ?? ''))
                    ->schema([
                        Fields::twoColumns([
                            TextInput::make('lat')->label('Широта')->numeric()->required(),
                            TextInput::make('lng')->label('Долгота')->numeric()->required(),
                            TextInput::make('x')->label('x, px')->numeric()->required()->helperText('Проще — кнопкой «Указать на карте» ниже.'),
                            TextInput::make('y')->label('y, px')->numeric()->required(),
                        ]),
                        self::mapPoint(),
                    ]),
            ]),
            Placeholder::make('map_note')->hiddenLabel()->content('Не меняется отсюда: условные значки легенды (общие для всех карт) и файлы панорам (public/panoramas/{ключ}/).'),
        ];
    }

    public static function table(Table $table): Table
    {
        return Fields::table($table, [
            Fields::column('name', 'Локация'),
            TextColumn::make('city')->label('Город')->getStateUsing(fn ($record) => $record->t('city')),
            TextColumn::make('altitude_m')->label('Высота, м'),
            TextColumn::make('hotels_count')->label('Отели')->counts('hotels'),
            TextColumn::make('transports_count')->label('Транспорт')->counts('transports'),
        ]);
    }

    public static function getRelationManagers(): array
    {
        return [
            RelationManagers\HotelsRelationManager::class,
            RelationManagers\TransportsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLocations::route('/'),
            'create' => CreateLocation::route('/create'),
            'edit' => EditLocation::route('/{record}/edit'),
        ];
    }
}
