<?php

namespace App\Filament\Resources\Sugerencias\Pages;

use App\Filament\Resources\Sugerencias\SugerenciaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSugerencia extends EditRecord
{
    protected static string $resource = SugerenciaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
