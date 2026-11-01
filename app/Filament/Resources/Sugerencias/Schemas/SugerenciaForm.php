<?php

namespace App\Filament\Resources\Sugerencias\Schemas;

use App\Models\Sugerencia;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SugerenciaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Select::make('tipo')->options(Sugerencia::TIPOS)->disabled(),
                Select::make('estado')->options(Sugerencia::ESTADOS)->required(),
                Select::make('sitio_id')->label('Sitio afectado')->relationship('sitio', 'nombre')->disabled(),
                Select::make('user_id')->label('Enviada por')->relationship('user', 'name')->disabled(),
                TextInput::make('nombre')->label('Nombre del sitio propuesto')->disabled(),
                TextInput::make('municipio')->disabled(),
                Select::make('provincia_id')->label('Provincia')->relationship('provincia', 'nombre')->disabled(),
                Textarea::make('mensaje')->rows(6)->columnSpanFull()->disabled(),
            ]);
    }
}
