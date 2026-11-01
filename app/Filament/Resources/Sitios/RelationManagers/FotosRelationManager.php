<?php

namespace App\Filament\Resources\Sitios\RelationManagers;

use App\Filament\Resources\Fotos\Schemas\FotoForm;
use App\Filament\Resources\Fotos\Tables\FotosTable;
use App\Support\Wikimedia;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

// Fotos dentro de la ficha de un sitio: subir, ordenar (arrastrando), publicar y buscar fotos libres
class FotosRelationManager extends RelationManager
{
    protected static string $relationship = 'fotos';

    protected static ?string $title = 'Fotos de este sitio';

    protected static string|\BackedEnum|null $icon = 'heroicon-o-photo';

    protected static ?string $modelLabel = 'foto';

    protected static ?string $pluralModelLabel = 'fotos';

    public static function getBadge(Model $ownerRecord, string $pageClass): ?string
    {
        $n = $ownerRecord->fotos()->where('publicada', false)->count();

        return $n ? $n.' por revisar' : null;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components(FotoForm::campos(conSitio: false));
    }

    public function table(Table $table): Table
    {
        return FotosTable::configure($table, enSitio: true)
            ->defaultSort('orden')
            ->reorderable('orden')
            ->description('La primera publicada es la principal de la ficha. Para cambiar el orden, pulsa el icono de ordenar y arrastra.')
            ->emptyStateHeading('Este sitio aún no tiene fotos')
            ->emptyStateDescription('Sube una tuya o cedida por el sitio, o pulsa «Buscar fotos libres en Wikimedia».')
            ->headerActions([
                CreateAction::make()
                    ->label('Añadir una foto')
                    ->icon('heroicon-o-plus')
                    ->mutateDataUsing(fn (array $data) => [...$data, 'user_id' => auth()->id()])
                    ->successNotificationTitle('Foto guardada'),
                Action::make('wikimedia')
                    ->label('Buscar fotos libres en Wikimedia')
                    ->icon('heroicon-o-magnifying-glass')
                    ->color('gray')
                    ->tooltip('Busca fotos con licencia libre de este sitio. Se guardan sin publicar para que las revises.')
                    ->action(function () {
                        $nuevas = Wikimedia::crearCandidatas($this->getOwnerRecord());
                        Notification::make()
                            ->title($nuevas ? "{$nuevas} fotos encontradas" : 'No hay fotos libres de este sitio')
                            ->body($nuevas
                                ? 'Están abajo, sin publicar. Mira que sean de este sitio y que no salga nadie reconocible, y pulsa «Publicar» en la que te guste.'
                                : 'Prueba a pedírselas al propio sitio o a hacer una tú.')
                            ->{$nuevas ? 'success' : 'warning'}()
                            ->send();
                    }),
            ]);
    }
}
