<?php

namespace App\Filament\Support;

use App\Support\Locales;
use App\Support\Property;
use Filament\Forms\Components\Select;
use Closure;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

/**
 * Общие поля админки: вкладки языков, слаг, SEO, публикация, фото.
 */
class Fields
{
    /**
     * Вкладки языков. $fields получает код языка и признак основного языка
     * и возвращает поля вида TextInput::make("title.$locale").
     *
     * @param  Closure(string $locale, bool $isDefault): array  $fields
     */
    public static function translatable(Closure $fields, string $id = 'translations'): Tabs
    {
        $tabs = [];
        foreach (Locales::all() as $code => $meta) {
            $isDefault = $code === Locales::source();
            $tabs[] = Tab::make($meta['name'])
                ->id("$id-$code")
                ->badge($isDefault ? 'исходный' : null)
                ->schema($fields($code, $isDefault));
        }

        return Tabs::make($id)->tabs($tabs)->columnSpanFull();
    }

    /** Поле названия на основном языке, которое заполняет слаг при создании. */
    public static function titleInput(string $attribute, string $locale, bool $isDefault, string $label = 'Название'): TextInput
    {
        $input = TextInput::make("$attribute.$locale")
            ->label($label)
            ->maxLength(190)
            ->required($isDefault);

        if ($isDefault) {
            $input->live(onBlur: true)
                ->afterStateUpdated(function (Get $get, Set $set, ?string $state, string $operation) {
                    if ($operation === 'create' && blank($get('slug'))) {
                        $set('slug', Str::slug((string) $state));
                    }
                });
        }

        return $input;
    }

    /**
     * Привязка к локации (отелю). Поле видно всегда, в том числе при одном отеле:
     * номера и рестораны обязаны принадлежать конкретной локации, предложения и впечатления
     * можно оставить общими для всех локаций. По умолчанию подставляется главный отель.
     */
    public static function hotel(bool $required = false): Select
    {
        $select = Select::make('hotel_id')->label('Локация (отель)')
            ->relationship('hotel', 'slug')
            ->getOptionLabelFromRecordUsing(fn ($record) => $record->t('name'))
            ->default(fn () => Property::primary()?->id)
            ->native(false)
            ->required($required);

        return $required
            ? $select->helperText('К какой локации относится запись.')
            : $select->placeholder('Все локации')->helperText('Оставьте пустым, если запись относится ко всем локациям сети.');
    }

    /** Колонка «Локация» в таблицах. */
    public static function hotelColumn(): TextColumn
    {
        return TextColumn::make('hotel_id')->label('Локация')
            ->getStateUsing(fn ($record) => $record->hotel?->t('name'))
            ->placeholder('Все локации')
            ->badge()->color('gray');
    }

    /** Фильтр таблицы по локации. */
    public static function hotelFilter(): \Filament\Tables\Filters\SelectFilter
    {
        return \Filament\Tables\Filters\SelectFilter::make('hotel_id')->label('Локация')
            ->relationship('hotel', 'slug')
            ->getOptionLabelFromRecordUsing(fn ($record) => $record->t('name'));
    }

    public static function slug(array $reserved = []): TextInput
    {
        return TextInput::make('slug')
            ->label('Адрес (слаг)')
            ->helperText('Латиница, цифры и дефис. Один для всех языков.')
            ->required()
            ->maxLength(120)
            ->regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/')
            ->notIn($reserved)
            ->validationMessages(['not_in' => 'Этот адрес занят разделом сайта.']);
    }

    public static function seo(): Section
    {
        return Section::make('SEO')
            ->description('Если пусто — берутся название и краткое описание.')
            ->collapsed()
            ->schema([
                self::translatable(fn (string $l) => [
                    TextInput::make("seo_title.$l")->label('Title')->maxLength(70),
                    Textarea::make("seo_description.$l")->label('Description')->rows(2)->maxLength(170),
                ], 'seo'),
                self::image('og_image', 'Картинка для соцсетей (1200×630)', 'og'),
            ]);
    }

    public static function publishing(bool $featured = false): Section
    {
        return Section::make('Публикация')
            ->schema(array_filter([
                Toggle::make('is_published')->label('Опубликовано')->default(true),
                $featured ? Toggle::make('is_featured')->label('Показывать на главной') : null,
                TextInput::make('sort')->label('Порядок')->numeric()->default(0),
            ]));
    }

    public static function image(string $name, string $label, string $directory): FileUpload
    {
        return FileUpload::make($name)
            ->label($label)
            ->image()
            ->imageEditor()
            ->disk('public')
            ->directory($directory)
            ->maxSize(8192)
            ->saveUploadedFileUsing(fn ($file) => \App\Support\ImageVariants::store($file, $directory));
    }

    public static function gallery(string $name, string $directory): FileUpload
    {
        return self::image($name, 'Галерея', $directory)
            ->multiple()
            ->reorderable()
            ->panelLayout('grid')
            ->columnSpanFull();
    }

    /** Колонка таблицы для переводимого поля. */
    public static function column(string $attribute, string $label): TextColumn
    {
        return TextColumn::make($attribute)
            ->label($label)
            ->getStateUsing(fn ($record) => $record->t($attribute))
            ->searchable(query: fn ($query, string $search) => $query->where($attribute, 'like', "%{$search}%"))
            ->wrap();
    }

    public static function publishedColumn(): IconColumn
    {
        return IconColumn::make('is_published')->label('Опубл.')->boolean();
    }

    public static function table(Table $table, array $columns, array $filters = []): Table
    {
        return $table
            ->columns($columns)
            ->filters($filters)
            ->defaultSort('sort')
            ->reorderable('sort')
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function twoColumns(array $components): Grid
    {
        return Grid::make(2)->schema($components)->columnSpanFull();
    }
}
