<?php

namespace App\Filament\Resources\Transports\Pages;

use App\Filament\Resources\Transports\TransportResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTransport extends EditRecord
{
    protected static string $resource = TransportResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
