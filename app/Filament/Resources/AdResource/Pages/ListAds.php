<?php

namespace App\Filament\Resources\AdResource\Pages;

use App\Filament\Resources\AdResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAds extends ListRecords
{
    protected static string $resource = AdResource::class;

    protected function getHeaderActions(): array
    {
        return [
           
        ];
    }
}
