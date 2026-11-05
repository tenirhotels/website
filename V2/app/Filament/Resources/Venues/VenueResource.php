<?php

namespace App\Filament\Resources\Venues;

use App\Filament\Resources\Venues\Pages\CreateVenue;
use App\Filament\Resources\Venues\Pages\EditVenue;
use App\Filament\Resources\Venues\Pages\ListVenues;
use App\Filament\Support\Fields;
use App\Models\Venue;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class VenueResource extends Resource
{
    // В админке адреса записей по id, на сайте — по слагу.
    protected static ?string $recordRouteKeyName = 'id';

    protected static ?string $model = Venue::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCake;

    protected static string|UnitEnum|null $navigationGroup = 'Отели';

    protected static ?string $modelLabel = 'ресторан';

    protected static ?string $pluralModelLabel = 'Рестораны и бары';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Fields::twoColumns([
                Select::make('hotel_id')->label('Отель')->required()
                    ->relationship('hotel', 'slug')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->t('name')),
                Fields::slug(),
                Select::make('type')->label('Тип')->required()->default('restaurant')->options([
                    'restaurant' => 'Ресторан', 'bar' => 'Бар', 'terrace' => 'Терраса',
                ]),
            ]),
            Fields::translatable(fn (string $l, bool $d) => [
                Fields::titleInput('name', $l, $d),
                Textarea::make("summary.$l")->label('Кратко')->rows(2),
                RichEditor::make("description.$l")->label('Описание'),
                TextInput::make("hours.$l")->label('Часы работы')->placeholder('Завтрак 08:30–11:30 · ужин 18:00–22:00'),
            ]),
            Section::make('Меню и фото')->columnSpanFull()->schema([
                FileUpload::make('menu_file')->label('Меню (PDF)')->disk('public')->directory('menus')
                    ->acceptedFileTypes(['application/pdf']),
                Fields::image('image', 'Обложка', 'venues'),
                Fields::gallery('gallery', 'venues/gallery'),
            ]),
            Fields::seo(),
            Fields::publishing(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return Fields::table($table, [
            Fields::column('name', 'Название'),
            TextColumn::make('hotel.slug')->label('Отель'),
            TextColumn::make('type')->label('Тип'),
            Fields::publishedColumn(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVenues::route('/'),
            'create' => CreateVenue::route('/create'),
            'edit' => EditVenue::route('/{record}/edit'),
        ];
    }
}
