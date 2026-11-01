<?php

namespace App\Filament\Resources\Sitios\Pages;

use App\Filament\Resources\Sitios\SitioResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSitio extends EditRecord
{
    protected static string $resource = SitioResource::class;

    // Las fotos van en una pestaña arriba, al lado de los datos, en vez de al final de la página
    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }

    public function getContentTabLabel(): ?string
    {
        return 'Datos del sitio';
    }

    public function getContentTabIcon(): string
    {
        return 'heroicon-o-pencil-square';
    }

    public function getSubheading(): ?string
    {
        return 'Cambia lo que haga falta y pulsa «Guardar cambios» al final. Cada cambio queda apuntado en el Historial.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('ver')
                ->label('Ver en la web')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('gray')
                ->url(fn () => route('sitio', $this->record), shouldOpenInNewTab: true),
            DeleteAction::make()
                ->label('Borrar')
                ->color('gray')
                ->modalHeading('¿Borrar este sitio para siempre?')
                ->modalDescription('Si solo ha cerrado o no quieres que se vea, mejor desactiva «Se ve en la web»: así no pierdes lo que sabías de él.')
                ->modalSubmitActionLabel('Sí, borrarlo'),
        ];
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Guardado. Ya se ve así en la web.';
    }
}
