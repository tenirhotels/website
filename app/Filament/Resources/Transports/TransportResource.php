<?php

namespace App\Filament\Resources\Transports;

use App\Filament\Resources\Transports\Pages\CreateTransport;
use App\Filament\Resources\Transports\Pages\EditTransport;
use App\Filament\Resources\Transports\Pages\ListTransports;
use App\Filament\Support\Fields;
use App\Models\Transport;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

/** Транспорт локации: линии канатной дороги, особый подъём для гостей, трансфер, такси. Принадлежит локации: у каждого места свой. */
class TransportResource extends Resource
{
    use \App\Filament\Support\AdminOnly;

    use \App\Filament\Support\UsesIdInUrls;

    protected static ?string $recordRouteKeyName = 'id';

    protected static ?string $model = Transport::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    protected static string|UnitEnum|null $navigationGroup = 'Отели';

    protected static ?string $modelLabel = 'транспорт';

    protected static ?string $pluralModelLabel = 'Транспорт';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Fields::twoColumns([
                Select::make('location_id')->label('Локация')->required()->native(false)
                    ->relationship('location', 'slug')->getOptionLabelFromRecordUsing(fn ($record) => $record->t('name'))
                    ->default(fn () => \App\Models\Location::orderBy('sort')->orderBy('id')->value('id')),
                Select::make('kind')->label('Вид')->required()->live()->default('cable_line')->options(Transport::KINDS)
                    ->helperText('Линии M-1, K-1, K-2 и особый подъём для гостей показываются на странице «Как добраться» с калькулятором. Остальное — списком.'),
                Select::make('code')->label('Линия')->options(array_combine(array_keys(Transport::CABLE_CODES), array_map(fn ($c, $r) => "$c · $r", array_keys(Transport::CABLE_CODES), Transport::CABLE_CODES)))
                    ->visible(fn (Get $get) => $get('kind') === 'cable_line')->required(fn (Get $get) => $get('kind') === 'cable_line'),
                TextInput::make('hours')->label('Часы работы')->placeholder('09:00–18:00')
                    ->helperText(fn (Get $get) => $get('kind') === 'guest_ride' ? 'Окно отправления, например 18:00–18:30.' : 'Расписание задаёт перевозчик; меняйте при смене сезона.'),
            ]),
            Fields::translatable(fn (string $l, bool $d) => [
                TextInput::make("name.$l")->label('Название')->placeholder('Такси до Медеу')
                    ->helperText('Для линий канатной дороги не нужно: название берётся из станций. У такси и трансфера можно оставить пустым — заголовком станет «Откуда — Куда».'),
                TextInput::make("route_from.$l")->label('Откуда')->placeholder('Аэропорт Алматы')
                    ->visible(fn (Get $get) => in_array($get('kind'), ['taxi', 'shuttle'], true)),
                TextInput::make("route_to.$l")->label('Куда')->placeholder('Станция Медеу')
                    ->visible(fn (Get $get) => in_array($get('kind'), ['taxi', 'shuttle'], true)),
                Textarea::make("summary.$l")->label('Описание')->rows(2),
                TextInput::make("note.$l")->label('Пояснение (сезон, дни)')->placeholder('ежедневно, будни и выходные'),
                TextInput::make("price.$l")->label('Цена')->placeholder('от 8 000 ₸ за машину')
                    ->helperText('Текстом, как вы хотите показать гостю: сумма, за что (за машину, с человека). Пусто — цена не показывается.')
                    ->visible(fn (Get $get) => ! in_array($get('kind'), ['cable_line', 'guest_ride'], true)),
            ]),
            Fields::twoColumns([
                Toggle::make('orderable')->label('Кнопка «Заказать»')->default(true)
                    ->helperText('Ведёт на контакты отеля: WhatsApp с готовым сообщением, телефон, e-mail (Отель → Локация и контакты).')
                    ->visible(fn (Get $get) => ! in_array($get('kind'), ['cable_line', 'guest_ride'], true)),
                Select::make('map_pins')->label('Точки на карте')->multiple()->native(false)
                    ->options(function (Get $get): array {
                        $map = (array) (\App\Models\Location::find($get('location_id'))?->map ?? []);

                        return collect($map['pins'] ?? [])->whereIn('kind', ['place', 'station'])
                            ->mapWithKeys(fn ($pin) => [$pin['id'] => ($pin['label']['ru'] ?? null) ?: $pin['id']])->all();
                    })
                    ->helperText('Например, зоны высадки. В карточке появятся ссылки на эти точки на карте курорта.')
                    ->visible(fn (Get $get) => ! in_array($get('kind'), ['cable_line', 'guest_ride'], true)),
            ]),
            Fields::image('image', 'Фото', 'transport')
                ->helperText('Для линии канатной дороги — фото станции, откуда она идёт. Показывается в карточке поездки, по нажатию открывается крупно.'),
            Fields::publishing(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return Fields::table($table, [
            TextColumn::make('code')->label('Линия')->badge(),
            TextColumn::make('name')->label('Название')->getStateUsing(fn ($record) => $record->title()),
            TextColumn::make('kind')->label('Вид')->formatStateUsing(fn ($state) => Transport::KINDS[$state] ?? $state),
            TextColumn::make('location_id')->label('Локация')->getStateUsing(fn ($record) => $record->location?->t('name'))->badge()->color('gray'),
            TextColumn::make('hours')->label('Часы'),
            Fields::publishedColumn(),
        ], [
            \Filament\Tables\Filters\SelectFilter::make('location_id')->label('Локация')->relationship('location', 'slug')->getOptionLabelFromRecordUsing(fn ($record) => $record->t('name')),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTransports::route('/'),
            'create' => CreateTransport::route('/create'),
            'edit' => EditTransport::route('/{record}/edit'),
        ];
    }
}
