<?php

namespace App\Filament\Resources\RoomTypes;

use App\Filament\Resources\RoomTypes\Pages\CreateRoomType;
use App\Filament\Resources\RoomTypes\Pages\EditRoomType;
use App\Filament\Resources\RoomTypes\Pages\ListRoomTypes;
use App\Filament\Support\Fields;
use App\Models\RoomType;
use App\Support\Property;
use BackedEnum;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
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
    use \App\Filament\Support\UsesIdInUrls;

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
            ]),
            Section::make('Параметры')->columns(4)->columnSpanFull()->schema([
                TextInput::make('size_m2')->label('Площадь, м²')->numeric(),
                TextInput::make('max_guests')->label('Гостей макс.')->numeric(),
                TextInput::make('price_from')->label('Цена от, ₸')->numeric()
                    ->helperText('Пусто — «Проверить цены»'),
                TextInput::make('exely_room_type_id')->label('ID типа номера в Exely')
                    ->helperText('Для кнопки бронирования, напр. 62361'),
            ]),
            Section::make('Особенности номера')->columnSpanFull()->description('Один список для всего: параметры (площадь, гости, кровать, вид) и удобства. Порядок — перетаскиванием. «Выделить крупно» выносит пункт наверх карточки крупным шрифтом, выделить можно до 3; остальное идёт списком с иконками.')->schema([
                Repeater::make('features')->hiddenLabel()->addActionLabel('Добавить пункт')->collapsed()->cloneable()->reorderable()
                    ->rules([fn () => function (string $attribute, $value, \Closure $fail) {
                        $n = collect((array) $value)->where('pin', true)->count();
                        if ($n > RoomType::MAX_TILES) {
                            $fail('Выделено крупно '.$n.' — можно не больше '.RoomType::MAX_TILES.'. Снимите лишние отметки.');
                        }
                    }])
                    ->itemLabel(function (array $state) {
                        $src = $state['source'] ?? 'custom';
                        $name = RoomType::FACTS[$src] ?? (\App\Support\Locales::pick((array) ($state['text'] ?? [])) ?? 'Новый пункт');

                        return trim((($state['pin'] ?? false) ? '★ ' : '').$name);
                    })
                    ->schema([
                        Select::make('source')->label('Что это')->native(false)->live()->default('custom')->required()
                            ->options(['custom' => 'Своё удобство (свой текст)'] + RoomType::FACTS)
                            ->helperText('Площадь, гости, кровать и вид берутся из полей номера и переводятся сами.'),
                        Select::make('icon')->label('Иконка')->options(self::iconOptions())->allowHtml()->native(false)->default('leaf')->required(),
                        Toggle::make('pin')->label('Выделить крупно')->inline(false),
                        Fields::translatable(fn (string $l) => [TextInput::make("text.$l")->label('Название')], 'feat-text')
                            ->visible(fn (Get $get) => ($get('source') ?? 'custom') === 'custom'),
                    ]),
            ]),
            Section::make('Фото')->columnSpanFull()->schema([
                Fields::image('image', 'Обложка', 'rooms'),
                Fields::gallery('gallery', 'rooms/gallery'),
            ]),
            Fields::seo(),
            Fields::publishing(),
        ]);
    }

    /** @return array<string,string> имя иконки => подпись с картинкой */
    public static function iconOptions(): array
    {
        $labels = ['sauna' => 'Сауна', 'window' => 'Окно', 'module' => 'Модуль', 'ski' => 'Ski-in / ski-out', 'bed' => 'Кровать', 'wifi' => 'Wi-Fi', 'tv' => 'Телевизор',
            'minibar' => 'Мини-бар', 'coffee' => 'Кофе, завтрак', 'robe' => 'Халат', 'bath' => 'Душ, ванная', 'locker' => 'Локер', 'view' => 'Вид, горы', 'size' => 'Площадь',
            'guests' => 'Гости', 'heat' => 'Отопление', 'car' => 'Авто', 'leaf' => 'Эко / другое'];
        $out = [];
        foreach ($labels as $k => $label) {
            $out[$k] = '<span style="display:flex;align-items:center;gap:10px;white-space:nowrap">'
                .'<span style="display:block;flex:none;width:20px;height:20px">'.str_replace('class="icon-x"', 'style="width:100%;height:100%"', (string) site_icon($k, 'icon-x')).'</span>'
                .'<span style="white-space:nowrap">'.e($label).'</span></span>';
        }

        return $out;
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
