<?php

namespace App\Filament\Resources\Sitios\Pages;

use App\Filament\Resources\Sitios\SitioResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSitios extends ListRecords
{
    protected static string $resource = SitioResource::class;

    public function getSubheading(): ?string
    {
        return 'Todos los lugares de la guía. Pulsa en uno para cambiarlo. Con el embudo de la derecha puedes ver solo algunos: los que no salen en el mapa, los que caducan pronto…';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Añadir un sitio')->icon('heroicon-o-plus'),
        ];
    }
}
