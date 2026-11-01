<?php

namespace App\Filament\Resources\Cambios\Tables;

use App\Models\Cambio;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CambiosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('fecha')->date('d/m/Y')->sortable(),
                TextColumn::make('sitio_nombre')->label('Sitio')->searchable()->wrap(),
                TextColumn::make('municipio')->searchable(),
                TextColumn::make('campo'),
                TextColumn::make('antes')->limit(60)->wrap()->placeholder('—'),
                TextColumn::make('despues')->label('Después')->limit(60)->wrap()->placeholder('—'),
                TextColumn::make('fuente')->label('Quién / fuente')->limit(40),
            ])
            ->filters([
                SelectFilter::make('campo')->options(fn () => Cambio::query()->distinct()->orderBy('campo')->pluck('campo', 'campo')->all()),
            ]);
    }
}
