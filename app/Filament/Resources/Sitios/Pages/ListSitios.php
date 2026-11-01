<?php

namespace App\Filament\Resources\Sitios\Pages;

use App\Filament\Resources\Sitios\SitioResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSitios extends ListRecords
{
    protected static string $resource = SitioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
