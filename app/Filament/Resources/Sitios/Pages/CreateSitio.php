<?php

namespace App\Filament\Resources\Sitios\Pages;

use App\Filament\Resources\Sitios\SitioResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSitio extends CreateRecord
{
    protected static string $resource = SitioResource::class;

    protected static ?string $title = 'Añadir un sitio';

    public function getSubheading(): ?string
    {
        return 'Lo obligatorio lleva un asterisco rojo. Lo demás, rellénalo si lo sabes: siempre puedes completarlo después.';
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Sitio añadido. Ya sale en la web.';
    }
}
