<?php

namespace App\Filament\Resources\Transports;

use App\Filament\Resources\Transports\Pages\CreateTransport;
use App\Filament\Resources\Transports\Pages\EditTransport;
use App\Filament\Resources\Transports\Pages\ListTransports;
use App\Filament\Support\Fields;
use App\Models\Transport;
use BackedEnum;
use Filament\Forms\Components\Select;
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
                TextInput::make("name.$l")->label('Название')->placeholder('Трансфер из аэропорта')
                    ->helperText('Для линий канатной дороги не нужно: название берётся из станций.'),
                Textarea::make("summary.$l")->label('Описание')->rows(2),
                TextInput::make("note.$l")->label('Пояснение (сезон, дни)')->placeholder('ежедневно, будни и выходные'),
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
            Fields::column('name', 'Название'),
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
