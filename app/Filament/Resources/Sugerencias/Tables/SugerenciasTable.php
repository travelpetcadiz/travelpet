<?php

namespace App\Filament\Resources\Sugerencias\Tables;

use App\Filament\Resources\Sitios\SitioResource;
use App\Models\Sugerencia;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SugerenciasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('No hay sugerencias por revisar')
            ->emptyStateDescription('Cuando alguien proponga un sitio o avise de un dato mal, aparecerá aquí. Para ver las ya hechas, cambia el filtro del embudo.')
            ->emptyStateIcon('heroicon-o-light-bulb')
            ->columns([
                TextColumn::make('tipo')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => Sugerencia::TIPOS[$state] ?? $state)
                    ->color(fn (string $state) => $state === 'error' ? 'danger' : 'info'),
                TextColumn::make('sitio')
                    ->label('Sitio')
                    ->state(fn (Sugerencia $record) => $record->sitio?->nombre ?? trim($record->nombre.' ('.collect([$record->municipio, $record->provincia?->nombre])->filter()->implode(', ').')', ' ()'))
                    ->wrap(),
                TextColumn::make('mensaje')->limit(90)->wrap()->searchable(),
                TextColumn::make('user.name')->label('Enviada por')->placeholder('Anónimo'),
                TextColumn::make('estado')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => Sugerencia::ESTADOS[$state] ?? $state)
                    ->color(fn (string $state) => match ($state) {
                        'hecha' => 'success',
                        'descartada' => 'gray',
                        default => 'warning',
                    }),
                TextColumn::make('created_at')->label('Fecha')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('estado')->label('Mostrar')->options(Sugerencia::ESTADOS)->default('pendiente'),
                SelectFilter::make('tipo')->options(Sugerencia::TIPOS),
            ])
            ->recordActions([
                Action::make('abrirSitio')
                    ->label('Corregir el sitio')
                    ->icon('heroicon-o-pencil-square')
                    ->visible(fn (Sugerencia $record) => $record->sitio_id !== null)
                    ->url(fn (Sugerencia $record) => SitioResource::getUrl('edit', ['record' => $record->sitio])),
                Action::make('hecha')
                    ->label('Marcar hecha')
                    ->tooltip('Ya lo has revisado o corregido')
                    ->successNotificationTitle('Marcada como hecha')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (Sugerencia $record) => $record->estado === 'pendiente')
                    ->action(fn (Sugerencia $record) => $record->update(['estado' => 'hecha'])),
                EditAction::make()->label('Leer entera'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
