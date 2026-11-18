<?php

namespace App\Filament\Resources\Locations\RelationManagers;

use App\Filament\Resources\Hotels\HotelResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

/** Вкладка «Отели» в карточке локации: отели, привязанные к месту. */
class HotelsRelationManager extends RelationManager
{
    protected static string $relationship = 'hotels';

    protected static ?string $relatedResource = HotelResource::class;

    protected static ?string $title = 'Отели';

    public function table(Table $table): Table
    {
        return $table->headerActions([CreateAction::make()]);
    }
}
