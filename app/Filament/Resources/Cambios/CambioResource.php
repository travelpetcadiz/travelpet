<?php

namespace App\Filament\Resources\Cambios;

use App\Filament\Resources\Cambios\Pages\ListCambios;
use App\Filament\Resources\Cambios\Tables\CambiosTable;
use App\Models\Cambio;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CambioResource extends Resource
{
    protected static ?string $model = Cambio::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static ?string $modelLabel = 'cambio';

    protected static ?string $pluralModelLabel = 'historial de cambios';

    protected static ?string $navigationLabel = 'Historial';

    protected static ?int $navigationSort = 5;

    public static function table(Table $table): Table
    {
        return CambiosTable::configure($table);
    }

    // Solo lectura: se rellena solo al editar sitios
    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCambios::route('/'),
        ];
    }
}
