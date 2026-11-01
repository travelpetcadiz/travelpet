<?php

namespace App\Filament\Resources\Resenas\Pages;

use App\Filament\Resources\Resenas\ResenaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditResena extends EditRecord
{
    protected static string $resource = ResenaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
