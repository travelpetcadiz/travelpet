<?php

namespace App\Filament\Resources\Sugerencias;

use App\Filament\Resources\Sugerencias\Pages\EditSugerencia;
use App\Filament\Resources\Sugerencias\Pages\ListSugerencias;
use App\Filament\Resources\Sugerencias\Schemas\SugerenciaForm;
use App\Filament\Resources\Sugerencias\Tables\SugerenciasTable;
use App\Models\Sugerencia;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SugerenciaResource extends Resource
{
    protected static ?string $model = Sugerencia::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLightBulb;

    protected static ?string $modelLabel = 'sugerencia';

    protected static ?string $pluralModelLabel = 'sugerencias';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return SugerenciaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SugerenciasTable::configure($table);
    }

    public static function getNavigationBadge(): ?string
    {
        $n = Sugerencia::where('estado', 'pendiente')->count();

        return $n ? (string) $n : null;
    }

    // Llegan desde el formulario público
    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSugerencias::route('/'),
            'edit' => EditSugerencia::route('/{record}/edit'),
        ];
    }
}
