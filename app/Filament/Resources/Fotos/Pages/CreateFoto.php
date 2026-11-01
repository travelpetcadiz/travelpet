<?php

namespace App\Filament\Resources\Fotos\Pages;

use App\Filament\Resources\Fotos\FotoResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateFoto extends CreateRecord
{
    protected static string $resource = FotoResource::class;

    protected static ?string $title = 'Añadir una foto';

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()->label('Guardar la foto');
    }

    protected function getCreateAnotherFormAction(): Action
    {
        return parent::getCreateAnotherFormAction()->label('Guardar y añadir otra');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Foto guardada. Si la has publicado, ya sale en la ficha del sitio.';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        return $data;
    }
}
