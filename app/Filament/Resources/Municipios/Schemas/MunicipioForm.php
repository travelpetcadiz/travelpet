<?php

namespace App\Filament\Resources\Municipios\Schemas;

use App\Models\Municipio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MunicipioForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components(static::campos());
    }

    // También se usan para crear un municipio sin salir del formulario de un sitio
    public static function campos(): array
    {
        return [
            Select::make('provincia_id')->label('Provincia')->relationship('provincia', 'nombre')->required()->preload()->searchable(),
            TextInput::make('nombre')->required()->maxLength(255),
            TextInput::make('comarca')
                ->datalist(fn () => Municipio::query()->whereNotNull('comarca')->distinct()->orderBy('comarca')->pluck('comarca')->all()),
            TextInput::make('latitud')->numeric(),
            TextInput::make('longitud')->numeric(),
        ];
    }
}
