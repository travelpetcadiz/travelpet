<?php

namespace App\Filament\Resources\Fotos\Pages;

use App\Filament\Resources\Fotos\FotoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFoto extends EditRecord
{
    protected static string $resource = FotoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
