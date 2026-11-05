<?php

namespace App\Filament\Resources\Experiences;

use App\Filament\Resources\Experiences\Pages\CreateExperience;
use App\Filament\Resources\Experiences\Pages\EditExperience;
use App\Filament\Resources\Experiences\Pages\ListExperiences;
use App\Filament\Support\Fields;
use App\Models\Experience;
use App\Support\Locales;
use BackedEnum;
use Filament\Forms\Components\CheckboxList;
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

class ExperienceResource extends Resource
{
    // В админке адреса записей по id, на сайте — по слагу.
    protected static ?string $recordRouteKeyName = 'id';

    protected static ?string $model = Experience::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static string|UnitEnum|null $navigationGroup = 'Контент';

    protected static ?string $modelLabel = 'впечатление';

    protected static ?string $pluralModelLabel = 'Впечатления';

    protected static ?int $navigationSort = 2;

    public static function categoryOptions(): array
    {
        return collect(config('tenir.experience_categories'))->map(fn ($l) => $l['ru'])->all();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Fields::twoColumns([
                Select::make('hotel_id')->label('Отель')
                    ->relationship('hotel', 'slug')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->t('name'))
                    ->placeholder('Вся сеть'),
                Fields::slug()->prefix('/experiences/'),
                Select::make('category')->label('Категория')->required()->options(self::categoryOptions()),
                CheckboxList::make('seasons')->label('Сезоны')->columns(4)
                    ->options(collect(config('tenir.seasons'))->map(fn ($l) => $l['ru'])->all()),
            ]),
            Fields::translatable(fn (string $l, bool $d) => [
                Fields::titleInput('title', $l, $d),
                Textarea::make("summary.$l")->label('Кратко')->rows(2),
                RichEditor::make("description.$l")->label('Описание'),
                TextInput::make("duration.$l")->label('Длительность')->placeholder('60 минут'),
            ]),
            Section::make('Цена и продажа')->columns(2)->columnSpanFull()->schema([
                TextInput::make('price_from')->label('Цена от, ₸')->numeric(),
                TextInput::make('exely_service_id')->label('ID доп. услуги в Exely')
                    ->helperText('Если услугу можно добавить при бронировании'),
            ]),
            Section::make('Фото')->columnSpanFull()->schema([
                Fields::image('image', 'Обложка', 'experiences'),
                Fields::gallery('gallery', 'experiences/gallery'),
            ]),
            Fields::seo(),
            Fields::publishing(featured: true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return Fields::table($table, [
            Fields::column('title', 'Впечатление'),
            TextColumn::make('category')->label('Категория')->badge()
                ->formatStateUsing(fn (string $state) => self::categoryOptions()[$state] ?? $state),
            TextColumn::make('hotel.slug')->label('Отель')->placeholder('Вся сеть'),
            Fields::publishedColumn(),
        ], [
            SelectFilter::make('category')->label('Категория')->options(self::categoryOptions()),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListExperiences::route('/'),
            'create' => CreateExperience::route('/create'),
            'edit' => EditExperience::route('/{record}/edit'),
        ];
    }
}
