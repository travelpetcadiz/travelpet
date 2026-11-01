<?php

namespace App\Filament\Resources\Sitios\Pages;

use App\Filament\Resources\Sitios\SitioResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSitio extends EditRecord
{
    protected static string $resource = SitioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
