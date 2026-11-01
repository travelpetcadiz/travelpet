<?php

namespace App\Filament\Resources\Fotos\Pages;

use App\Filament\Resources\Fotos\FotoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFotos extends ListRecords
{
    protected static string $resource = FotoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
