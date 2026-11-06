<?php

namespace App\Filament\Resources\Events;

use App\Filament\Resources\Events\Pages\CreateEvent;
use App\Filament\Resources\Events\Pages\EditEvent;
use App\Filament\Resources\Events\Pages\ListEvents;
use App\Filament\Support\Fields;
use App\Models\Event;
use App\Models\Experience;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
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

/** События: календарь того, что планируется на курорте и в отелях. Прошедшие скрываются с сайта сами. */
class EventResource extends Resource
{
    use \App\Filament\Support\UsesIdInUrls;

    protected static ?string $recordRouteKeyName = 'id';

    protected static ?string $model = Event::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static string|UnitEnum|null $navigationGroup = 'Контент';

    protected static ?string $modelLabel = 'событие';

    protected static ?string $pluralModelLabel = 'События';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Fields::twoColumns([
                Fields::hotel(),
                Fields::slug()->prefix('/events/'),
            ]),
            Section::make('Когда')->columns(3)->columnSpanFull()->schema([
                DatePicker::make('starts_on')->label('Дата начала')->required(),
                DatePicker::make('ends_on')->label('Дата окончания')->afterOrEqual('starts_on')
                    ->helperText('Оставьте пустым для события одного дня'),
                TextInput::make('time_text')->label('Время')->placeholder('12:30–16:30'),
            ]),
            Fields::translatable(fn (string $l, bool $d) => [
                Fields::titleInput('title', $l, $d),
                Textarea::make("summary.$l")->label('Кратко')->rows(2),
                TextInput::make("place.$l")->label('Место')->placeholder('Терраса Tenir, 3 200 м'),
                RichEditor::make("description.$l")->label('Описание'),
            ]),
            Section::make('Кнопка и фото')->columns(2)->columnSpanFull()->schema([
                Select::make('cta')->label('Кнопка в карточке')->options(Experience::CTA)->default('booking')->required()->native(false)
                    ->helperText('Мероприятия для компаний — «запрос в отдел продаж»; открытые события — «без кнопки»'),
                Fields::image('image', 'Обложка', 'events'),
            ]),
            Fields::seo(),
            Fields::publishing(featured: false),
        ]);
    }

    public static function table(Table $table): Table
    {
        return Fields::table($table, [
            TextColumn::make('starts_on')->label('Дата')->date('d.m.Y')->sortable(),
            Fields::column('title', 'Событие'),
            Fields::hotelColumn(),
            Fields::publishedColumn(),
        ], [
            Fields::hotelFilter(),
        ])->reorderable(false)->defaultSort('starts_on', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEvents::route('/'),
            'create' => CreateEvent::route('/create'),
            'edit' => EditEvent::route('/{record}/edit'),
        ];
    }
}
