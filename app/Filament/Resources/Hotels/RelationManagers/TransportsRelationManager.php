<?php

namespace App\Filament\Resources\Hotels\RelationManagers;

use App\Filament\Resources\Transports\TransportResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

/** Вкладка «Транспорт» внутри карточки отеля: как добраться именно до этой локации. */
class TransportsRelationManager extends RelationManager
{
    protected static string $relationship = 'transports';

    protected static ?string $relatedResource = TransportResource::class;

    protected static ?string $title = 'Транспорт';

    public function table(Table $table): Table
    {
        return $table->headerActions([CreateAction::make()]);
    }
}
