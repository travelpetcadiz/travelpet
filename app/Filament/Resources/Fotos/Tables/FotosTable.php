<?php

namespace App\Filament\Resources\Fotos\Tables;

use App\Models\Foto;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class FotosTable
{
    // Publicar exige haber comprobado que no sale nadie reconocible: se pregunta en el mismo paso
    private static function publicar(): array
    {
        return [
            'modalHeading' => '¿Has mirado bien la foto?',
            'modalDescription' => 'Solo se publica si no sale ninguna persona que se pueda reconocer (ni niños). Si sale alguien de cerca, mejor busca otra foto.',
            'modalSubmitActionLabel' => 'Sí, no sale nadie: publicar',
            'datos' => ['sin_personas' => true, 'publicada' => true],
        ];
    }

    // $enSitio: la tabla va dentro de la ficha de un sitio (no hace falta decir de qué sitio es)
    public static function configure(Table $table, bool $enSitio = false): Table
    {
        $p = static::publicar();

        return $table
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn (Builder $query) => $query->with('sitio'))
            ->emptyStateHeading('Aún no hay fotos')
            ->emptyStateDescription('Sube fotos reales de los sitios (tuyas o cedidas por el propio sitio) o pega el enlace de una foto libre de Wikimedia Commons.')
            ->emptyStateIcon('heroicon-o-photo')
            ->columns([
                ImageColumn::make('archivo')->label('Foto')->disk('public')->imageHeight(72),
                TextColumn::make('descripcion')
                    ->label('Qué se ve')
                    ->weight('bold')
                    ->wrap()
                    ->searchable()
                    ->description(fn (Foto $record) => $enSitio ? null : ($record->sitio ? 'En la ficha de '.$record->sitio->nombre : 'Para el diseño de la web')),
                TextColumn::make('origen')
                    ->label('De dónde sale')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (string $state, Foto $record) => match ($state) {
                        'wikimedia' => 'Wikimedia'.($record->licencia ? ' · '.$record->licencia : ''),
                        'cedida' => 'Cedida por el sitio',
                        default => 'Propia',
                    }),
                TextColumn::make('publicada')
                    ->label('¿Se ve en la web?')
                    ->badge()
                    ->formatStateUsing(fn (bool $state) => $state ? 'Sí' : 'Por revisar')
                    ->color(fn (bool $state) => $state ? 'success' : 'warning'),
            ])
            ->filters([
                TernaryFilter::make('publicada')
                    ->label('¿Se ve en la web?')
                    ->trueLabel('Solo las publicadas')
                    ->falseLabel('Solo las que faltan por revisar'),
                SelectFilter::make('origen')->label('De dónde sale')->options(Foto::ORIGENES),
                TernaryFilter::make('de_sitio')
                    ->label('Para qué es')
                    ->trueLabel('Fotos de sitios')
                    ->falseLabel('Fotos para el diseño de la web')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('sitio_id'),
                        false: fn (Builder $query) => $query->whereNull('sitio_id'),
                    ),
            ])
            ->recordActions([
                Action::make('publicar')
                    ->label('Publicar')
                    ->icon('heroicon-o-eye')
                    ->color('success')
                    ->visible(fn (Foto $record) => ! $record->publicada && $record->sitio_id)
                    ->requiresConfirmation()
                    ->modalHeading($p['modalHeading'])
                    ->modalDescription($p['modalDescription'])
                    ->modalSubmitActionLabel($p['modalSubmitActionLabel'])
                    ->action(fn (Foto $record) => $record->update($p['datos']))
                    ->successNotificationTitle('Publicada: ya sale en la ficha'),
                Action::make('ocultar')
                    ->label('Ocultar')
                    ->icon('heroicon-o-eye-slash')
                    ->color('gray')
                    ->visible(fn (Foto $record) => $record->publicada)
                    ->action(fn (Foto $record) => $record->update(['publicada' => false]))
                    ->successNotificationTitle('Ya no sale en la web'),
                EditAction::make()->label('Cambiar'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('publicar')
                        ->label('Publicar')
                        ->icon('heroicon-o-eye')
                        ->requiresConfirmation()
                        ->modalHeading('¿Has mirado bien todas las fotos marcadas?')
                        ->modalDescription($p['modalDescription'])
                        ->modalSubmitActionLabel($p['modalSubmitActionLabel'])
                        ->action(fn (Collection $records) => $records->each->update($p['datos'])),
                    BulkAction::make('ocultar')
                        ->label('Ocultar de la web')
                        ->icon('heroicon-o-eye-slash')
                        ->action(fn (Collection $records) => $records->each->update(['publicada' => false])),
                    DeleteBulkAction::make()->label('Borrar para siempre'),
                ])->label('Con las marcadas'),
            ]);
    }
}
