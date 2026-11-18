<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (($data['role'] ?? null) === 'admin') {
            $data['hotel_id'] = null;
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()->hidden(fn () => $this->record->is(auth()->user()))];
    }
}
