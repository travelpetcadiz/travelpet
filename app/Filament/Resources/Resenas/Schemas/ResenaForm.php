<?php

namespace App\Filament\Resources\Resenas\Schemas;

use App\Models\Resena;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ResenaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Select::make('sitio_id')->relationship('sitio', 'nombre')->disabled(),
                Select::make('user_id')->label('Autor')->relationship('user', 'name')->disabled(),
                TextInput::make('estrellas')->disabled(),
                Select::make('pudo_entrar')->label('¿Pudo entrar con su perro?')->options(['si' => 'Sí', 'no' => 'No'])->disabled(),
                Select::make('tamano_perro')->label('Tamaño del perro')->options(Resena::TAMANOS)->disabled(),
                Select::make('estado')->options(Resena::ESTADOS)->required(),
                Textarea::make('texto')->rows(6)->columnSpanFull()->helperText('Puedes corregir erratas o quitar datos personales antes de aprobar.'),
            ]);
    }
}
