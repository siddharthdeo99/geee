<?php

namespace App\Filament\Resources\VerificationCenterResource\Pages;

use App\Filament\Resources\VerificationCenterResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVerificationCenter extends EditRecord
{
    protected static string $resource = VerificationCenterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {

        if (isset($data['status'])) {
            if ($data['status'] == 'declined') {
                $data['declined_at'] = now();
            } elseif ($data['status'] == 'verified') {
                $data['verified_at'] = now();
            }
        }

        return $data;
    }

}
