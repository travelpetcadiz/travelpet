<?php

namespace App\Filament\Resources\Fotos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class FotosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageColumn::make('archivo')->label('Foto')->disk('public')->imageHeight(80),
                TextColumn::make('descripcion')->label('Qué se ve')->wrap()->searchable(),
                TextColumn::make('lugar')->label('Dónde')->placeholder('—'),
                IconColumn::make('para_portada')->label('Para la portada')->boolean(),
                TextColumn::make('created_at')->label('Subida')->date('d/m/Y')->sortable(),
            ])
            ->filters([
                TernaryFilter::make('para_portada')->label('Para la portada'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
