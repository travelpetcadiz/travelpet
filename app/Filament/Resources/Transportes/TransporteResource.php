<?php

namespace App\Filament\Resources\Transportes;

use App\Filament\Resources\Transportes\Pages\CreateTransporte;
use App\Filament\Resources\Transportes\Pages\EditTransporte;
use App\Filament\Resources\Transportes\Pages\ListTransportes;
use App\Filament\Resources\Transportes\Schemas\TransporteForm;
use App\Filament\Resources\Transportes\Tables\TransportesTable;
use App\Models\Transporte;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class TransporteResource extends Resource
{
    protected static ?string $model = Transporte::class;

    protected static ?string $slug = 'transporte';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    protected static string|UnitEnum|null $navigationGroup = 'La guía';

    protected static ?string $navigationLabel = 'Transporte con mascota';

    protected static ?string $modelLabel = 'compañía de transporte';

    protected static ?string $pluralModelLabel = 'transporte con mascota';

    protected static ?string $recordTitleAttribute = 'nombre';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return TransporteForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TransportesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTransportes::route('/'),
            'create' => CreateTransporte::route('/create'),
            'edit' => EditTransporte::route('/{record}/edit'),
        ];
    }
}
