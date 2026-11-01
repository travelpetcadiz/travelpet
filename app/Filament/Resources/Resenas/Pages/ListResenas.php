<?php

namespace App\Filament\Resources\Resenas\Pages;

use App\Filament\Resources\Resenas\ResenaResource;
use Filament\Resources\Pages\ListRecords;

class ListResenas extends ListRecords
{
    protected static string $resource = ResenaResource::class;

    public function getSubheading(): ?string
    {
        return 'Opiniones que escribe la gente en la web. No se ven hasta que pulsas «Publicar». Primero salen las que esperan revisión; con el embudo puedes ver también las ya publicadas.';
    }
}
