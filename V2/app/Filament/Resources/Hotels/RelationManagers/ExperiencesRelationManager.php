<?php

namespace App\Filament\Resources\Hotels\RelationManagers;

use App\Filament\Resources\Experiences\ExperienceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

/** Вкладка «Впечатления» внутри карточки отеля: записи этой локации. */
class ExperiencesRelationManager extends RelationManager
{
    protected static string $relationship = 'experiences';

    protected static ?string $relatedResource = ExperienceResource::class;

    protected static ?string $title = 'Впечатления';

    public function table(Table $table): Table
    {
        return $table->headerActions([CreateAction::make()]);
    }
}
