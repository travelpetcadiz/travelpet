<?php

namespace App\Filament\Resources\Provincias;

use App\Filament\Resources\Provincias\Pages\CreateProvincia;
use App\Filament\Resources\Provincias\Pages\EditProvincia;
use App\Filament\Resources\Provincias\Pages\ListProvincias;
use App\Filament\Resources\Provincias\Schemas\ProvinciaForm;
use App\Filament\Resources\Provincias\Tables\ProvinciasTable;
use App\Models\Provincia;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ProvinciaResource extends Resource
{
    protected static ?string $model = Provincia::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeEuropeAfrica;

    protected static string|UnitEnum|null $navigationGroup = 'Ajustes';

    protected static ?string $modelLabel = 'provincia';

    protected static ?string $pluralModelLabel = 'provincias';

    protected static ?string $recordTitleAttribute = 'nombre';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return ProvinciaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProvinciasTable::configure($table);
    }

    // Abrir provincias nuevas es decisión de los administradores
    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProvincias::route('/'),
            'create' => CreateProvincia::route('/create'),
            'edit' => EditProvincia::route('/{record}/edit'),
        ];
    }
}
