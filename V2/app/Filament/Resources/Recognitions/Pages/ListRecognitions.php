<?php

namespace App\Filament\Resources\Recognitions\Pages;

use App\Filament\Resources\Recognitions\RecognitionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRecognitions extends ListRecords
{
    protected static string $resource = RecognitionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
