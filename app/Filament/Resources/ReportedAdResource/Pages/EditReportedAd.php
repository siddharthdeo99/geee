<?php

namespace App\Filament\Resources\ReportedAdResource\Pages;

use App\Filament\Resources\ReportedAdResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReportedAd extends EditRecord
{
    protected static string $resource = ReportedAdResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
