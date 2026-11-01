<?php

namespace App\Filament\Resources\Fotos\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class FotoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                FileUpload::make('archivo')
                    ->label('Foto')
                    ->disk('public')
                    ->directory('fotos')
                    ->image()
                    ->maxSize(12 * 1024)
                    ->required()
                    ->columnSpanFull()
                    ->helperText('Arrastra aquí la foto o pulsa para elegirla (hasta 12 MB). Mejor en horizontal, con buena luz y hecha por ti o con permiso de quien la hizo.'),
                TextInput::make('descripcion')
                    ->label('Qué se ve')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Mi perra Luna tumbada en la arena de la playa de Bateles')
                    ->helperText('Una frase sencilla. La leen las personas ciegas con su lector de pantalla, y también Google.')
                    ->columnSpanFull(),
                TextInput::make('lugar')
                    ->label('Dónde está hecha')
                    ->placeholder('Conil de la Frontera'),
                TextInput::make('autor')
                    ->label('Quién la hizo')
                    ->default(fn () => auth()->user()?->name),
                Toggle::make('para_portada')
                    ->label('Me gustaría que saliera en la portada')
                    ->helperText('No se publica sola: sirve para elegir las fotos del nuevo diseño.'),
            ]);
    }
}
