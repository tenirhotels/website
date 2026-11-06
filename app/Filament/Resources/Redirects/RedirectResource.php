<?php

namespace App\Filament\Resources\Redirects;

use App\Filament\Resources\Redirects\Pages\CreateRedirect;
use App\Filament\Resources\Redirects\Pages\EditRedirect;
use App\Filament\Resources\Redirects\Pages\ListRedirects;
use App\Models\Redirect;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class RedirectResource extends Resource
{
    protected static ?string $model = Redirect::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowUturnRight;

    protected static string|UnitEnum|null $navigationGroup = 'Сайт';

    protected static ?string $modelLabel = 'редирект';

    protected static ?string $pluralModelLabel = 'Редиректы';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('from_path')->label('Старый адрес')->required()->placeholder('/en-gb/rooms')
                ->helperText('Без домена. Параметры (?room-type=…) переносятся автоматически.')
                ->unique(ignoreRecord: true),
            TextInput::make('to_path')->label('Новый адрес')->required()->placeholder('/en/destinations/shymbulak/rooms'),
            Select::make('status_code')->label('Тип')->default(301)->options([301 => '301 — навсегда', 302 => '302 — временно']),
            Toggle::make('is_active')->label('Активен')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('from_path')->label('Откуда')->searchable(),
                TextColumn::make('to_path')->label('Куда')->searchable(),
                TextColumn::make('status_code')->label('Код'),
                TextColumn::make('hits')->label('Переходов')->sortable(),
                TextColumn::make('last_hit_at')->label('Последний')->since()->sortable(),
                IconColumn::make('is_active')->label('Актив.')->boolean(),
            ])
            ->defaultSort('from_path')
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRedirects::route('/'),
            'create' => CreateRedirect::route('/create'),
            'edit' => EditRedirect::route('/{record}/edit'),
        ];
    }
}
