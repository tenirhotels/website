<?php

namespace App\Filament\Resources\Offers;

use App\Filament\Resources\Offers\Pages\CreateOffer;
use App\Filament\Resources\Offers\Pages\EditOffer;
use App\Filament\Resources\Offers\Pages\ListOffers;
use App\Filament\Support\Fields;
use App\Models\Offer;
use App\Support\Property;
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

class OfferResource extends Resource
{
    use \App\Filament\Support\UsesIdInUrls;

    // В админке адреса записей по id, на сайте — по слагу.
    protected static ?string $recordRouteKeyName = 'id';

    protected static ?string $model = Offer::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static string|UnitEnum|null $navigationGroup = 'Контент';

    protected static ?string $modelLabel = 'предложение';

    protected static ?string $pluralModelLabel = 'Предложения';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        $n = \App\Models\Offer::active()->count();

        return $n > 0 ? (string) $n : null;
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Действуют сегодня';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Fields::twoColumns([
                Fields::hotel(),
                Fields::slug()->prefix('/offers/'),
            ]),
            Fields::translatable(fn (string $l, bool $d) => [
                Fields::titleInput('title', $l, $d),
                Textarea::make("summary.$l")->label('1. Короткое описание (для карточек)')->rows(2)->maxLength(200)->required($d),
                RichEditor::make("description.$l")->label('2. Полное описание (на странице предложения)')->required($d),
                RichEditor::make("included.$l")->label('3. Что входит в пакет (можно не заполнять)')->helperText('Если пусто — блок не показывается. Заголовок добавляется автоматически.'),
                RichEditor::make("terms.$l")->label('4. Условия (можно не заполнять)')->helperText('Если пусто — блок не показывается.'),
            ]),
            Section::make('Бронирование и сроки')->columns(3)->columnSpanFull()->schema([
                TextInput::make('exely_special_offer_id')->label('ID предложения в Exely')
                    ->helperText('Напр. 10137078 — кнопка ведёт сразу на тариф'),
                TextInput::make('promo_code')->label('Промокод'),
                TextInput::make('min_nights')->label('Минимум ночей')->numeric(),
                DatePicker::make('valid_from')->label('Действует с'),
                DatePicker::make('valid_to')->label('Действует до')->afterOrEqual('valid_from'),
            ]),
            Section::make('Фото')->columnSpanFull()->schema([Fields::image('image', 'Фото', 'offers')->required()]),
            Fields::seo(),
            Fields::publishing(featured: true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return Fields::table($table, [
            Fields::column('title', 'Предложение'),
            Fields::hotelColumn(),
            TextColumn::make('exely_special_offer_id')->label('Exely ID'),
            TextColumn::make('valid_to')->label('До')->date('d.m.Y')->placeholder('бессрочно'),
            Fields::publishedColumn(),
        ], [
            Fields::hotelFilter(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOffers::route('/'),
            'create' => CreateOffer::route('/create'),
            'edit' => EditOffer::route('/{record}/edit'),
        ];
    }
}
