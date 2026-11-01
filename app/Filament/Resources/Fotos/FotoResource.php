<?php

namespace App\Filament\Resources\Fotos;

use App\Filament\Resources\Fotos\Pages\CreateFoto;
use App\Filament\Resources\Fotos\Pages\EditFoto;
use App\Filament\Resources\Fotos\Pages\ListFotos;
use App\Filament\Resources\Fotos\Schemas\FotoForm;
use App\Filament\Resources\Fotos\Tables\FotosTable;
use App\Models\Foto;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class FotoResource extends Resource
{
    protected static ?string $model = Foto::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static string|UnitEnum|null $navigationGroup = 'La guía';

    protected static ?string $modelLabel = 'foto';

    protected static ?string $pluralModelLabel = 'fotos';

    protected static ?string $recordTitleAttribute = 'descripcion';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return FotoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FotosTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFotos::route('/'),
            'create' => CreateFoto::route('/create'),
            'edit' => EditFoto::route('/{record}/edit'),
        ];
    }
}
