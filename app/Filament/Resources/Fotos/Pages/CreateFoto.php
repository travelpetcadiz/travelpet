<?php

namespace App\Filament\Resources\Fotos\Pages;

use App\Filament\Resources\Fotos\FotoResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFoto extends CreateRecord
{
    protected static string $resource = FotoResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        return $data;
    }
}
