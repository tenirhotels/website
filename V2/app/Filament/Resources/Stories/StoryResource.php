<?php

namespace App\Filament\Resources\Stories;

use App\Filament\Resources\Stories\Pages\CreateStory;
use App\Filament\Resources\Stories\Pages\EditStory;
use App\Filament\Resources\Stories\Pages\ListStories;
use App\Filament\Support\Fields;
use App\Models\Story;
use BackedEnum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class StoryResource extends Resource
{
    // В админке адреса записей по id, на сайте — по слагу.
    protected static ?string $recordRouteKeyName = 'id';

    protected static ?string $model = Story::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    protected static string|UnitEnum|null $navigationGroup = 'Контент';

    protected static ?string $modelLabel = 'история';

    protected static ?string $pluralModelLabel = 'Истории и новости';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Fields::twoColumns([
                Fields::slug()->prefix('/stories/'),
                Fields::hotel(),
                DateTimePicker::make('published_at')->label('Дата публикации')->default(now()),
            ]),
            Fields::image('image', 'Обложка', 'stories')->columnSpanFull(),
            Fields::translatable(fn (string $l, bool $d) => [
                Fields::titleInput('title', $l, $d, 'Заголовок'),
                Textarea::make("summary.$l")->label('Анонс')->rows(2),
                RichEditor::make("body.$l")->label('Текст'),
            ]),
            Fields::seo(),
            Fields::publishing(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return Fields::table($table, [
            Fields::column('title', 'Заголовок'),
            TextColumn::make('published_at')->label('Дата')->dateTime('d.m.Y')->sortable(),
            Fields::publishedColumn(),
        ])->defaultSort('published_at', 'desc')->reorderable(false);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStories::route('/'),
            'create' => CreateStory::route('/create'),
            'edit' => EditStory::route('/{record}/edit'),
        ];
    }
}
