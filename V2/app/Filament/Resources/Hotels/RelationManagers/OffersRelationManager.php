<?php

namespace App\Filament\Resources\Hotels\RelationManagers;

use App\Filament\Resources\Offers\OfferResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

/** Вкладка «Предложения» внутри карточки отеля: записи этой локации. */
class OffersRelationManager extends RelationManager
{
    protected static string $relationship = 'offers';

    protected static ?string $relatedResource = OfferResource::class;

    protected static ?string $title = 'Предложения';

    public function table(Table $table): Table
    {
        return $table->headerActions([CreateAction::make()]);
    }
}
