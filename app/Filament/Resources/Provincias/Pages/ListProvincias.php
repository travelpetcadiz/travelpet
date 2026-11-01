<?php

namespace App\Filament\Resources\Provincias\Pages;

use App\Filament\Resources\Provincias\ProvinciaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProvincias extends ListRecords
{
    protected static string $resource = ProvinciaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
