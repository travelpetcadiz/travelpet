<?php

namespace App\Filament\Resources\Sitios\Tables;

use App\Models\Provincia;
use App\Models\Sitio;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
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
            ->modifyQueryUsing(fn (Builder $query) => $query->with('municipio'))
            ->paginated([25, 50, 100])
            ->defaultPaginationPageOption(25)
            ->searchPlaceholder('Buscar por nombre o pueblo')
            ->emptyStateHeading('No hay sitios con estos filtros')
            ->emptyStateDescription('Quita algún filtro (la X junto a «Filtros activos») o añade un sitio nuevo.')
            ->emptyStateIcon('heroicon-o-map-pin')
            ->columns([
                TextColumn::make('nombre')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    // Tipo, qué es y pueblo debajo del nombre: así la tabla cabe en la pantalla
                    ->description(fn (Sitio $record) => collect([$record->etiquetaTipo(), $record->subtipo, $record->municipio?->nombre])->filter()->implode(' · '))
                    ->wrap(),
                TextColumn::make('tipo')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => Sitio::TIPOS[$state][0] ?? $state)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                // Va debajo del nombre; la columna sigue existiendo para poder buscar y ordenar por pueblo
                TextColumn::make('municipio.nombre')->label('Pueblo')->searchable()->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('municipio.provincia.nombre')->label('Provincia')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('admite_perros')
                    ->label('¿Admite mascotas?')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => Sitio::ADMITE[$state] ?? $state)
                    ->color(fn (string $state) => match ($state) {
                        'si' => 'success',
                        'condiciones' => 'warning',
                        'no' => 'danger',
                        default => 'gray',
                    }),
                // Sin confirmar no es un error: gris, no rojo
                IconColumn::make('verificado')
                    ->label('Confirmado')
                    ->boolean()
                    ->falseIcon('heroicon-o-minus')
                    ->falseColor('gray')
                    ->tooltip(fn (Sitio $record) => $record->verificado ? 'Lo ha confirmado el propio sitio' : 'Aún no lo ha confirmado el propio sitio')
                    ->sortable(),
                TextColumn::make('fecha_comprobacion')
                    ->label('Comprobado')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn (Sitio $record) => $record->esAntiguo() ? 'danger' : null)
                    ->tooltip(fn (Sitio $record) => $record->esAntiguo() ? 'Hace más de 6 meses: en la web sale como dato antiguo' : null),
                ToggleColumn::make('publicado')
                    ->label('Se ve en la web')
                    ->tooltip('Apágalo para ocultar el sitio sin borrarlo'),
            ])
            ->filters([
                SelectFilter::make('tipo')->options(collect(Sitio::TIPOS)->mapWithKeys(fn ($v, $k) => [$k => $v[0]])),
                SelectFilter::make('municipio_id')
                    ->label('Pueblo')
                    ->relationship('municipio', 'nombre')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('provincia')
                    ->options(fn () => Provincia::orderBy('nombre')->pluck('nombre', 'id')->all())
                    ->query(fn (Builder $query, array $data) => $query->when($data['value'], fn (Builder $q, $id) => $q->whereRelation('municipio', 'provincia_id', $id))),
                SelectFilter::make('admite_perros')->label('¿Admite mascotas?')->options(Sitio::ADMITE),
                TernaryFilter::make('verificado')
                    ->label('Confirmado por el propio sitio')
                    ->trueLabel('Solo los confirmados')
                    ->falseLabel('Solo los que faltan por confirmar'),
                TernaryFilter::make('publicado')
                    ->label('Se ve en la web')
                    ->trueLabel('Solo los que se ven')
                    ->falseLabel('Solo los ocultos'),
                Filter::make('caducan')
                    ->label('Caducan pronto (comprobados hace más de 4 meses)')
                    ->query(fn (Builder $query) => $query->where('fecha_comprobacion', '<', now()->subDays(Sitio::DIAS_ANTIGUO - 60))),
                Filter::make('sin_coordenadas')
                    ->label('No salen en el mapa (les falta la ubicación)')
                    ->query(fn (Builder $query) => $query->sinUbicacion()),
                TernaryFilter::make('a_domicilio')
                    ->label('Servicio a domicilio')
                    ->trueLabel('Solo los que van a domicilio')
                    ->falseLabel('Solo los que tienen local'),
                Filter::make('playas_sin_fechas')
                    ->label('Playas sin fechas')
                    ->query(fn (Builder $query) => $query->where('tipo', 'Playa')->where('admite_perros', '!=', 'no')->whereNull('periodo_tipo')),
            ])
            ->recordActions([
                Action::make('verificar')
                    ->label('Confirmar')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->tooltip('Lo has comprobado con el propio sitio: se marca como confirmado con la fecha de hoy')
                    ->visible(fn (Sitio $record) => ! $record->verificado)
                    ->requiresConfirmation()
                    ->modalHeading('¿Lo has comprobado con el propio sitio?')
                    ->modalDescription('Se marca como confirmado con la fecha de hoy. Si algo ha cambiado, corrígelo antes entrando en el sitio.')
                    ->modalSubmitActionLabel('Sí, confirmar')
                    ->action(fn (Sitio $record) => $record->update(['verificado' => true, 'fecha_comprobacion' => now()]))
                    ->successNotificationTitle('Confirmado con fecha de hoy'),
                ActionGroup::make([
                    EditAction::make()->label('Cambiar datos'),
                    Action::make('ver')
                        ->label('Ver en la web')
                        ->icon('heroicon-o-arrow-top-right-on-square')
                        ->url(fn (Sitio $record) => route('sitio', $record), shouldOpenInNewTab: true),
                ])->label('Más')->tooltip('Más opciones'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('publicar')
                        ->label('Mostrar en la web')
                        ->icon('heroicon-o-eye')
                        ->action(fn (Collection $records) => $records->each->update(['publicado' => true])),
                    BulkAction::make('ocultar')
                        ->label('Ocultar de la web')
                        ->icon('heroicon-o-eye-slash')
                        ->action(fn (Collection $records) => $records->each->update(['publicado' => false])),
                    DeleteBulkAction::make()->label('Borrar para siempre'),
                ])->label('Con los marcados'),
            ]);
    }
}
