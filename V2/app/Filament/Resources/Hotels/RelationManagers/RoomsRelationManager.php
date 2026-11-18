<?php

namespace App\Filament\Resources\Hotels\RelationManagers;

use App\Filament\Resources\RoomTypes\RoomTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

/** Вкладка «Номера» внутри карточки отеля: записи этой локации. */
class RoomsRelationManager extends RelationManager
{
    protected static string $relationship = 'rooms';

    protected static ?string $relatedResource = RoomTypeResource::class;

    protected static ?string $title = 'Номера';

    public function table(Table $table): Table
    {
        return $table->headerActions([CreateAction::make()]);
    }
}
