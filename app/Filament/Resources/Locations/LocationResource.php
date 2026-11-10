<?php

namespace App\Filament\Resources\Locations;

use App\Filament\Resources\Locations\Pages\CreateLocation;
use App\Filament\Resources\Locations\Pages\EditLocation;
use App\Filament\Resources\Locations\Pages\ListLocations;
use App\Filament\Support\Fields;
use App\Models\Location;
use BackedEnum;
use Filament\Forms\Components\RichEditor;
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
            ]),
        ]);
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
