<?php

namespace App\Filament\Resources\Locations\RelationManagers;

use App\Filament\Resources\Transports\TransportResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

/** Вкладка «Транспорт» в карточке локации: как добраться именно сюда. */
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
