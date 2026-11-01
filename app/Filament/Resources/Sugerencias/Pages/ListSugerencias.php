<?php

namespace App\Filament\Resources\Sugerencias\Pages;

use App\Filament\Resources\Sugerencias\SugerenciaResource;
use Filament\Resources\Pages\ListRecords;

class ListSugerencias extends ListRecords
{
    protected static string $resource = SugerenciaResource::class;

    public function getSubheading(): ?string
    {
        return 'Sitios nuevos que propone la gente y avisos de datos que están mal. Compruébalo, cambia lo que haga falta en Sitios y pulsa «Marcar hecha».';
    }
}
