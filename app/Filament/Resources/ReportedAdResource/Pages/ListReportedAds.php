<?php

namespace App\Filament\Resources\ReportedAdResource\Pages;

use App\Filament\Resources\ReportedAdResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReportedAds extends ListRecords
{
    protected static string $resource = ReportedAdResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
