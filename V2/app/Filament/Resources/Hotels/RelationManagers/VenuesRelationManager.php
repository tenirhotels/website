<?php

namespace App\Filament\Resources\Hotels\RelationManagers;

use App\Filament\Resources\Venues\VenueResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

/** Вкладка «Рестораны и бары» внутри карточки отеля: записи этой локации. */
class VenuesRelationManager extends RelationManager
{
    protected static string $relationship = 'venues';

    protected static ?string $relatedResource = VenueResource::class;

    protected static ?string $title = 'Рестораны и бары';

    public function table(Table $table): Table
    {
        return $table->headerActions([CreateAction::make()]);
    }
}
