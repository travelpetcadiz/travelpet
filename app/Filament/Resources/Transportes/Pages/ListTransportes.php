<?php

namespace App\Filament\Resources\Transportes\Pages;

use App\Filament\Resources\Transportes\TransporteResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTransportes extends ListRecords
{
    protected static string $resource = TransporteResource::class;

    public function getTitle(): string
    {
        return 'Transporte con mascota';
    }

    public function getSubheading(): ?string
    {
        return 'Las aerolíneas, trenes, autobuses y barcos de la página «Transporte con mascota». Cuando una compañía cambie sus normas o precios, entra y corrígelo: se ve en la web al guardar.';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Añadir una compañía')->icon('heroicon-o-plus'),
        ];
    }
}
