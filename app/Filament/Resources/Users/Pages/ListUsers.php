<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    public function getSubheading(): ?string
    {
        return 'Las cuentas. Administrador: puede con todo. Editor: cambia sitios, reseñas y sugerencias, pero no usuarios ni provincias. Usuario: solo usa la web (reseñas y Mi viaje).';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Añadir un usuario')->icon('heroicon-o-plus'),
        ];
    }
}
