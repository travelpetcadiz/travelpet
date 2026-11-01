<?php

namespace App\Filament\Resources\Sitios\Tables;

use App\Models\Provincia;
use App\Models\Sitio;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class SitiosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('nombre')
            ->columns([
                TextColumn::make('nombre')->searchable()->sortable()->wrap(),
                TextColumn::make('tipo')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => Sitio::TIPOS[$state][0] ?? $state)
                    ->sortable(),
                TextColumn::make('municipio.nombre')->label('Municipio')->searchable()->sortable(),
                TextColumn::make('municipio.provincia.nombre')->label('Provincia')->sortable()->toggleable(),
                TextColumn::make('municipio.comarca')->label('Comarca')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('admite_perros')
                    ->label('Admite')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => Sitio::ADMITE[$state] ?? $state)
                    ->color(fn (string $state) => match ($state) {
                        'si' => 'success',
                        'condiciones' => 'warning',
                        'no' => 'danger',
                        default => 'gray',
                    }),
                IconColumn::make('verificado')->boolean()->sortable(),
                TextColumn::make('fecha_comprobacion')->label('Comprobado')->date('d/m/Y')->sortable(),
                ToggleColumn::make('publicado'),
            ])
            ->filters([
                SelectFilter::make('tipo')->options(collect(Sitio::TIPOS)->mapWithKeys(fn ($v, $k) => [$k => $v[0]])),
                SelectFilter::make('provincia')
                    ->options(fn () => Provincia::orderBy('nombre')->pluck('nombre', 'id')->all())
                    ->query(fn (Builder $query, array $data) => $query->when($data['value'], fn (Builder $q, $id) => $q->whereRelation('municipio', 'provincia_id', $id))),
                SelectFilter::make('municipio_id')
                    ->label('Municipio')
                    ->relationship('municipio', 'nombre')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('admite_perros')->label('Admite perros')->options(Sitio::ADMITE),
                TernaryFilter::make('verificado'),
                TernaryFilter::make('publicado'),
                Filter::make('sin_coordenadas')
                    ->label('Sin coordenadas (no salen en el mapa)')
                    ->query(fn (Builder $query) => $query->where(fn (Builder $q) => $q->whereNull('latitud')->orWhereNull('longitud'))),
            ])
            ->recordActions([
                Action::make('verificar')
                    ->label('Verificar')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn (Sitio $record) => ! $record->verificado)
                    ->requiresConfirmation()
                    ->modalDescription('Confirma que has comprobado los datos directamente con el sitio. Se guarda la fecha de hoy.')
                    ->action(fn (Sitio $record) => $record->update(['verificado' => true, 'fecha_comprobacion' => now()])),
                Action::make('ver')
                    ->label('Ver en la web')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (Sitio $record) => route('sitio', $record), shouldOpenInNewTab: true),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('publicar')
                        ->icon('heroicon-o-eye')
                        ->action(fn (Collection $records) => $records->each->update(['publicado' => true])),
                    BulkAction::make('ocultar')
                        ->icon('heroicon-o-eye-slash')
                        ->action(fn (Collection $records) => $records->each->update(['publicado' => false])),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
