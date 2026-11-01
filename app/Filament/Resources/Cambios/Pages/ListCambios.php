<?php

namespace App\Filament\Resources\Cambios\Pages;

use App\Filament\Resources\Cambios\CambioResource;
use Filament\Resources\Pages\ListRecords;

class ListCambios extends ListRecords
{
    protected static string $resource = CambioResource::class;

    public function getTitle(): string
    {
        return 'Historial de cambios';
    }

    public function getSubheading(): ?string
    {
        return 'Cada cambio que se ha hecho en un sitio: qué había antes, qué hay ahora, quién y cuándo. Si algo se cambió por error, aquí ves el valor anterior para volver a ponerlo.';
    }
}
