<?php

namespace App\Filament\Resources\Transportes\Tables;

use App\Models\Transporte;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class TransportesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('orden')
            ->defaultGroup('medio')
            ->groupingSettingsHidden()
            ->paginated([25, 50, 100])
            ->defaultPaginationPageOption(50)
            ->groups([
                Group::make('medio')->label('Tipo')->titlePrefixedWithLabel(false)
                    ->orderQueryUsing(fn ($query, string $direction) => $query->orderByRaw("case medio when 'avion' then 1 when 'tren' then 2 when 'autobus' then 3 else 4 end"))
                    ->getTitleFromRecordUsing(fn (Transporte $record) => Transporte::MEDIOS[$record->medio][0] ?? $record->medio),
            ])
            ->columns([
                TextColumn::make('nombre')->searchable()->description(fn (Transporte $record) => $record->zona),
                TextColumn::make('estado')->label('Respuesta')->badge()
                    ->formatStateUsing(fn (Transporte $record) => $record->textoRespuesta())
                    ->color(fn (string $state) => match ($state) {
                        'si' => 'success', 'condiciones' => 'warning', 'no' => 'danger', default => 'gray',
                    })
                    ->description(fn (Transporte $record) => $record->fecha_comprobacion
                        ? 'Comprobado el '.$record->fecha_comprobacion->format('j/n/Y')
                        : 'Pendiente de comprobar'),
                TextColumn::make('orden')->label('Posición')->sortable()->toggleable(isToggledHiddenByDefault: true),
                ToggleColumn::make('publicado')->label('En la web'),
            ])
            ->filters([
                SelectFilter::make('estado')->label('Respuesta')->options(Transporte::ESTADOS),
            ])
            ->emptyStateHeading('Aún no hay compañías')
            ->emptyStateDescription('Añade la primera con «Añadir una compañía».')
            ->recordActions([
                EditAction::make()->label('Corregir'),
            ]);
    }
}
