<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\User;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

/** Кто входит в админку: администраторы и редакторы отдельных локаций. Только для администратора. */
class UserResource extends Resource
{
    use \App\Filament\Support\AdminOnly;

    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static string|UnitEnum|null $navigationGroup = 'Сайт';

    protected static ?string $modelLabel = 'пользователь';

    protected static ?string $pluralModelLabel = 'Пользователи';

    protected static ?int $navigationSort = 90;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label('Имя')->required()->maxLength(255),
            TextInput::make('email')->label('Эл. почта (логин)')->email()->required()->maxLength(255)
                ->unique(ignoreRecord: true),
            TextInput::make('password')->label('Пароль')->password()->revealable()
                ->required(fn (string $operation) => $operation === 'create')
                ->minLength(8)
                ->dehydrated(fn (?string $state) => filled($state))
                ->helperText('При изменении оставьте пустым, чтобы не менять пароль.'),
            Select::make('role')->label('Роль')->native(false)->required()->live()
                ->options([
                    'admin' => 'Администратор — всё',
                    'editor' => 'Редактор локации — контент одного отеля',
                ])
                ->default('editor'),
            Select::make('hotel_id')->label('Локация редактора')
                ->relationship('hotel', 'slug')
                ->getOptionLabelFromRecordUsing(fn ($record) => $record->t('name'))
                ->native(false)
                ->visible(fn (Get $get) => $get('role') === 'editor')
                ->required(fn (Get $get) => $get('role') === 'editor')
                ->helperText('Видит и правит только события, предложения, впечатления, номера, рестораны, вопросы и новости этой локации.'),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Имя')->searchable(),
                TextColumn::make('email')->label('Почта')->searchable(),
                TextColumn::make('role')->label('Роль')->badge()
                    ->formatStateUsing(fn ($state) => $state === 'editor' ? 'Редактор' : 'Администратор')
                    ->color(fn ($state) => $state === 'editor' ? 'warning' : 'primary'),
                TextColumn::make('hotel_id')->label('Локация')
                    ->getStateUsing(fn ($record) => $record->hotel?->t('name'))
                    ->placeholder('—'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()->hidden(fn ($record) => $record->is(auth()->user())),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
