<?php

namespace App\Filament\Resources\RoomTypes;

use App\Filament\Resources\RoomTypes\Pages\CreateRoomType;
use App\Filament\Resources\RoomTypes\Pages\EditRoomType;
use App\Filament\Resources\RoomTypes\Pages\ListRoomTypes;
use App\Filament\Support\Fields;
use App\Models\RoomType;
use App\Support\Property;
use BackedEnum;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class RoomTypeResource extends Resource
{
    // В админке адреса записей по id, на сайте — по слагу.
    protected static ?string $recordRouteKeyName = 'id';

    protected static ?string $model = RoomType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHomeModern;

    protected static string|UnitEnum|null $navigationGroup = 'Отели';

    protected static ?string $modelLabel = 'номер';

    protected static ?string $pluralModelLabel = 'Номера';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Fields::twoColumns([
                Fields::hotel(required: true),
                Fields::slug(),
            ]),
            Fields::translatable(fn (string $l, bool $d) => [
                Fields::titleInput('name', $l, $d, 'Название номера'),
                Textarea::make("summary.$l")->label('Краткое описание')->rows(2),
                RichEditor::make("description.$l")->label('Описание'),
                TextInput::make("bed.$l")->label('Кровать')->placeholder('King-size'),
                TextInput::make("view.$l")->label('Вид')->placeholder('Панорама на горы'),
                TagsInput::make("amenities.$l")->label('Удобства')->placeholder('Финская сауна'),
            ]),
            Section::make('Параметры')->columns(4)->columnSpanFull()->schema([
                TextInput::make('size_m2')->label('Площадь, м²')->numeric(),
                TextInput::make('max_guests')->label('Гостей макс.')->numeric(),
                TextInput::make('price_from')->label('Цена от, ₸')->numeric()
                    ->helperText('Пусто — «Проверить цены»'),
                TextInput::make('exely_room_type_id')->label('ID типа номера в Exely')
                    ->helperText('Для кнопки бронирования, напр. 62361'),
            ]),
            Section::make('Фото')->columnSpanFull()->schema([
                Fields::image('image', 'Обложка', 'rooms'),
                Fields::gallery('gallery', 'rooms/gallery'),
            ]),
            Fields::seo(),
            Fields::publishing(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return Fields::table($table, [
            Fields::column('name', 'Номер'),
            Fields::hotelColumn(),
            TextColumn::make('exely_room_type_id')->label('Exely ID'),
            TextColumn::make('price_from')->label('От, ₸')->numeric(thousandsSeparator: ' '),
            Fields::publishedColumn(),
        ], [
            Fields::hotelFilter(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRoomTypes::route('/'),
            'create' => CreateRoomType::route('/create'),
            'edit' => EditRoomType::route('/{record}/edit'),
        ];
    }
}
