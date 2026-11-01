<?php

namespace App\Filament\Resources\Provincias\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class ProvinciasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('nombre')
            ->columns([
                TextColumn::make('nombre')->searchable()->sortable(),
                TextColumn::make('comunidad')->sortable(),
                TextColumn::make('municipios_count')->label('Municipios')->counts('municipios')->sortable(),
                TextColumn::make('sitios_count')->label('Sitios')->counts('sitios')->sortable(),
                ToggleColumn::make('activa'),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
