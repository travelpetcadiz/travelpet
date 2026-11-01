<?php

namespace App\Filament\Resources\Provincias\Pages;

use App\Filament\Resources\Provincias\ProvinciaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProvincias extends ListRecords
{
    protected static string $resource = ProvinciaResource::class;

    public function getSubheading(): ?string
    {
        return 'Las provincias de la guía. Para abrir una nueva, añádela y déjala desactivada mientras cargas sus sitios: no saldrá en la web hasta que la actives.';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Añadir una provincia')->icon('heroicon-o-plus'),
        ];
    }
}
