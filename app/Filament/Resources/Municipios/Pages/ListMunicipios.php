<?php

namespace App\Filament\Resources\Municipios\Pages;

use App\Filament\Resources\Municipios\MunicipioResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMunicipios extends ListRecords
{
    protected static string $resource = MunicipioResource::class;

    public function getSubheading(): ?string
    {
        return 'Los pueblos de la guía. Normalmente no hace falta tocarlos: al añadir un sitio puedes crear su pueblo con el «+».';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Añadir un pueblo')->icon('heroicon-o-plus'),
        ];
    }
}
