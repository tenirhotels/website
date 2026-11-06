<?php

namespace App\Filament\Resources\Recognitions;

use App\Filament\Resources\Recognitions\Pages\CreateRecognition;
use App\Filament\Resources\Recognitions\Pages\EditRecognition;
use App\Filament\Resources\Recognitions\Pages\ListRecognitions;
use App\Filament\Support\Fields;
use App\Models\Recognition;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class RecognitionResource extends Resource
{
    protected static ?string $model = Recognition::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTrophy;

    protected static string|UnitEnum|null $navigationGroup = 'Сайт';

    protected static ?string $modelLabel = 'признание';

    protected static ?string $pluralModelLabel = 'Награды и СМИ';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Fields::twoColumns([
                Select::make('kind')->label('Тип')->options(Recognition::KINDS)->default('award')->required()->selectablePlaceholder(false),
                TextInput::make('source')->label('Источник')->placeholder('MICHELIN Guide, Tatler Asia, Forbes…'),
                TextInput::make('year')->label('Год')->numeric()->minValue(1990)->maxValue(2100),
                TextInput::make('url')->label('Ссылка на подтверждение')->url()->helperText('Страница рейтинга или публикации — по ней гости и поисковики проверяют факт.'),
                TextInput::make('sort')->label('Порядок')->numeric()->default(0),
                Toggle::make('is_published')->label('Опубликовано')->default(true),
                Toggle::make('show_on_home')->label('Показывать на главной')->default(true),
            ]),
            Fields::translatable(fn (string $l, bool $d) => [
                TextInput::make("title.$l")->label('Заголовок')->required($d)->maxLength(120),
                TextInput::make("subtitle.$l")->label('Пояснение')->maxLength(200),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return Fields::table($table, [
            Fields::column('title', 'Заголовок'),
            TextColumn::make('source')->label('Источник'),
            TextColumn::make('year')->label('Год'),
            Fields::publishedColumn(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRecognitions::route('/'),
            'create' => CreateRecognition::route('/create'),
            'edit' => EditRecognition::route('/{record}/edit'),
        ];
    }
}
