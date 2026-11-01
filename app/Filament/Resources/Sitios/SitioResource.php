<?php

namespace App\Filament\Resources\Sitios;

use App\Filament\Resources\Sitios\Pages\CreateSitio;
use App\Filament\Resources\Sitios\Pages\EditSitio;
use App\Filament\Resources\Sitios\Pages\ListSitios;
use App\Filament\Resources\Sitios\Schemas\SitioForm;
use App\Filament\Resources\Sitios\Tables\SitiosTable;
use App\Models\Sitio;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class SitioResource extends Resource
{
    protected static ?string $model = Sitio::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    protected static ?string $modelLabel = 'sitio';

    protected static ?string $pluralModelLabel = 'sitios';

    protected static ?string $recordTitleAttribute = 'nombre';

    protected static string|UnitEnum|null $navigationGroup = 'La guía';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return SitioForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SitiosTable::configure($table);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['nombre', 'municipio.nombre'];
    }

    // Los editores pueden crear y editar; borrar solo los admins
    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\FotosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSitios::route('/'),
            'create' => CreateSitio::route('/create'),
            'edit' => EditSitio::route('/{record}/edit'),
        ];
    }
}
