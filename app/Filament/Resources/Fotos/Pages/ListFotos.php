<?php

namespace App\Filament\Resources\Fotos\Pages;

use App\Filament\Resources\Fotos\FotoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFotos extends ListRecords
{
    protected static string $resource = FotoResource::class;

    public function getSubheading(): ?string
    {
        return 'Las fotos de los sitios (salen en su ficha) y las del diseño de la web. Ninguna sale en la web hasta que pulsas «Publicar». También puedes añadirlas desde la ficha de cada sitio, abajo del todo.';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Subir una foto')->icon('heroicon-o-plus'),
        ];
    }
}
