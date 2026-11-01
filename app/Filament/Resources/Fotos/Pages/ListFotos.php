<?php

namespace App\Filament\Resources\Fotos\Pages;

use App\Filament\Resources\Fotos\FotoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFotos extends ListRecords
{
    protected static string $resource = FotoResource::class;

    public function getSubheading(): ?string
    {
        return 'Tus fotos reales para el diseño de la web. Al subirlas, marca las que te gustaría para la portada.';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Subir una foto')->icon('heroicon-o-plus'),
        ];
    }
}
