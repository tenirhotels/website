<?php

namespace App\Filament\Resources\Recognitions\Pages;

use App\Filament\Resources\Recognitions\RecognitionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRecognition extends EditRecord
{
    protected static string $resource = RecognitionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
