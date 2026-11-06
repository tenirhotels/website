<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\Pages\Pages\CreatePage;
use App\Filament\Resources\Pages\Pages\EditPage;
use App\Filament\Resources\Pages\Pages\ListPages;
use App\Filament\Support\Fields;
use App\Models\Page;
use BackedEnum;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

/**
 * Страницы бренда из блоков: «О бренде / Философия», Sustainability, правовые страницы.
 * У каждого языка свой набор блоков — тексты на разных языках часто разной длины и структуры.
 */
class PageResource extends Resource
{
    use \App\Filament\Support\UsesIdInUrls;

    // В админке адреса записей по id, на сайте — по слагу.
    protected static ?string $recordRouteKeyName = 'id';

    protected static ?string $model = Page::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|UnitEnum|null $navigationGroup = 'Контент';

    protected static ?string $modelLabel = 'страница';

    protected static ?string $pluralModelLabel = 'Страницы бренда';

    protected static ?int $navigationSort = 4;

    public static function blocks(): array
    {
        return [
            Block::make('intro')->label('Вступление')->icon(Heroicon::OutlinedSparkles)->schema([
                TextInput::make('eyebrow')->label('Надзаголовок'),
                TextInput::make('heading')->label('Заголовок')->required(),
                Textarea::make('text')->label('Текст')->rows(3),
            ]),
            Block::make('text')->label('Текст')->icon(Heroicon::OutlinedBars3BottomLeft)->schema([
                TextInput::make('heading')->label('Заголовок'),
                RichEditor::make('body')->label('Текст')->required(),
            ]),
            Block::make('image_text')->label('Фото + текст')->icon(Heroicon::OutlinedPhoto)->schema([
                TextInput::make('eyebrow')->label('Надзаголовок'),
                TextInput::make('heading')->label('Заголовок'),
                RichEditor::make('body')->label('Текст'),
                Fields::image('image', 'Фото', 'pages'),
                Select::make('image_side')->label('Фото')->options(['left' => 'Слева', 'right' => 'Справа'])->default('right'),
            ]),
            Block::make('quote')->label('Цитата')->icon(Heroicon::OutlinedChatBubbleBottomCenterText)->schema([
                Textarea::make('quote')->label('Цитата')->required()->rows(3),
                TextInput::make('caption')->label('Подпись'),
            ]),
            Block::make('word')->label('Слово бренда (Тәңір)')->icon(Heroicon::OutlinedLanguage)->schema([
                TextInput::make('word')->label('Слово')->default('Тәңір'),
                TextInput::make('meaning')->label('Значение')->default('«небо»'),
                Textarea::make('text')->label('Текст')->rows(3),
            ]),
            Block::make('pillars')->label('Принципы / столпы')->icon(Heroicon::OutlinedViewColumns)->schema([
                TextInput::make('eyebrow')->label('Надзаголовок'),
                TextInput::make('heading')->label('Заголовок'),
                Repeater::make('items')->label('Пункты')->schema([
                    TextInput::make('title')->label('Название')->required(),
                    Textarea::make('text')->label('Текст')->rows(2),
                ])->collapsible()->itemLabel(fn (array $state) => $state['title'] ?? null),
            ]),
            Block::make('stats')->label('Цифры')->icon(Heroicon::OutlinedChartBar)->schema([
                Repeater::make('items')->label('Цифры')->schema([
                    TextInput::make('value')->label('Значение')->required(),
                    TextInput::make('label')->label('Подпись')->required(),
                ])->maxItems(4)->grid(2),
            ]),
            Block::make('gallery')->label('Галерея')->icon(Heroicon::OutlinedSquares2x2)->schema([
                Fields::gallery('images', 'pages/gallery'),
            ]),
            Block::make('cta')->label('Призыв к действию')->icon(Heroicon::OutlinedCursorArrowRays)->schema([
                TextInput::make('heading')->label('Заголовок')->required(),
                Textarea::make('text')->label('Текст')->rows(2),
                TextInput::make('button_label')->label('Кнопка'),
                TextInput::make('button_url')->label('Ссылка')->helperText('booking — модуль бронирования, или адрес /ru/...'),
            ]),
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Fields::twoColumns([
                Fields::slug(config('tenir.reserved_slugs')),
                Select::make('placement')->label('Где показывать ссылку')->default('main')->options([
                    'main' => 'Главное меню и футер',
                    'footer' => 'Только футер',
                    'hidden' => 'Нигде (по прямой ссылке)',
                ]),
            ]),
            Fields::image('hero_image', 'Фото в шапке страницы', 'pages')->columnSpanFull(),
            Fields::translatable(fn (string $l, bool $d) => [
                Fields::titleInput('title', $l, $d, 'Заголовок страницы'),
                Textarea::make("summary.$l")->label('Подзаголовок')->rows(2),
                Builder::make("content.$l")->label('Блоки страницы')
                    ->blocks(self::blocks())
                    ->collapsible()->blockNumbers(false)->cloneable()
                    ->addActionLabel('Добавить блок'),
            ]),
            Fields::seo(),
            Fields::publishing(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return Fields::table($table, [
            Fields::column('title', 'Страница'),
            TextColumn::make('slug')->label('Адрес')->prefix('/'),
            TextColumn::make('placement')->label('Меню'),
            Fields::publishedColumn(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPages::route('/'),
            'create' => CreatePage::route('/create'),
            'edit' => EditPage::route('/{record}/edit'),
        ];
    }
}
