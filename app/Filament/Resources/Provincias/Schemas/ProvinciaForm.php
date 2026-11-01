<?php

namespace App\Filament\Resources\Provincias\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProvinciaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('nombre')->required()->maxLength(255),
                TextInput::make('slug')->helperText('Se usa en las direcciones web. Si lo dejas vacío se crea solo.')->unique(ignoreRecord: true),
                TextInput::make('comunidad')->placeholder('Andalucía'),
                TextInput::make('pais')->label('País (código)')->default('ES')->length(2)->required(),
                TextInput::make('latitud')->numeric()->helperText('Centro del mapa al elegir la provincia'),
                TextInput::make('longitud')->numeric(),
                TextInput::make('zoom')->numeric()->minValue(5)->maxValue(14)->default(9),
                Toggle::make('activa')->default(true)->helperText('Desactivada: sus sitios no salen en la web (útil mientras se prepara).'),
            ]);
    }
}
