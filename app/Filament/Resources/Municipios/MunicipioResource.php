<?php

namespace App\Filament\Resources\Municipios;

use App\Filament\Resources\Municipios\Pages\CreateMunicipio;
use App\Filament\Resources\Municipios\Pages\EditMunicipio;
use App\Filament\Resources\Municipios\Pages\ListMunicipios;
use App\Filament\Resources\Municipios\Schemas\MunicipioForm;
use App\Filament\Resources\Municipios\Tables\MunicipiosTable;
use App\Models\Municipio;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class MunicipioResource extends Resource
{
    protected static ?string $model = Municipio::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static string|UnitEnum|null $navigationGroup = 'Ajustes';

    protected static ?string $modelLabel = 'municipio';

    protected static ?string $pluralModelLabel = 'municipios';

    protected static ?string $recordTitleAttribute = 'nombre';

    protected static ?int $navigationSort = 11;

    public static function form(Schema $schema): Schema
    {
        return MunicipioForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MunicipiosTable::configure($table);
    }

    // Un municipio con sitios no se puede borrar (la base de datos lo impide)
    public static function canDelete(Model $record): bool
    {
        return (auth()->user()?->isAdmin() ?? false) && ! $record->sitios()->exists();
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMunicipios::route('/'),
            'create' => CreateMunicipio::route('/create'),
            'edit' => EditMunicipio::route('/{record}/edit'),
        ];
    }
}
