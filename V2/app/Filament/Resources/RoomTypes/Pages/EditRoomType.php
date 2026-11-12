<?php

namespace App\Filament\Resources\RoomTypes\Pages;

use App\Filament\Resources\RoomTypes\RoomTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRoomType extends EditRecord
{
    protected static string $resource = RoomTypeResource::class;

    /** Номер без нового списка: подставляем параметры и старые удобства, чтобы ничего не потерялось. */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (empty($data['features'])) {
            $data['features'] = $this->getRecord()->defaultFeatures();
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
