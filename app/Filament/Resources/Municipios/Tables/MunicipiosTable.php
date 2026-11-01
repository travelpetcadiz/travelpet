<?php

namespace App\Filament\Resources\Municipios\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MunicipiosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('nombre')
            ->columns([
                TextColumn::make('nombre')->searchable()->sortable(),
                TextColumn::make('provincia.nombre')->label('Provincia')->sortable(),
                TextColumn::make('comarca')->sortable()->placeholder('—'),
                TextColumn::make('sitios_count')->label('Sitios')->counts('sitios')->sortable(),
            ])
            ->filters([
                SelectFilter::make('provincia_id')->label('Provincia')->relationship('provincia', 'nombre'),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
