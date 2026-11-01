<?php

namespace App\Filament\Resources\Provincias\Pages;

use App\Filament\Resources\Provincias\ProvinciaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProvincia extends EditRecord
{
    protected static string $resource = ProvinciaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
