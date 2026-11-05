<?php

namespace App\Filament\Resources\Transportes\Pages;

use App\Filament\Resources\Transportes\TransporteResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTransporte extends EditRecord
{
    protected static string $resource = TransporteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->label('Borrar')->color('gray')
                ->modalDescription('Si solo quieres que no salga en la web, mejor desactiva «Se ve en la web»: así no pierdes los datos.'),
        ];
    }
}
