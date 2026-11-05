<?php

namespace App\Filament\Resources\Transportes\Schemas;

use App\Models\Transporte;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class TransporteForm
{
    public static function configure(Schema $schema): Schema
    {
        $esAvion = fn (Get $get) => $get('medio') === 'avion';

        return $schema->components([
            Section::make('La compañía')
                ->icon('heroicon-o-truck')
                ->description('Qué es y por dónde va.')
                ->columns(2)
                ->columnSpanFull()
                ->schema([
                    Select::make('medio')->label('Tipo de transporte')->required()->live()
                        ->options(collect(Transporte::MEDIOS)->map(fn ($m) => str($m[0])->after('En ')->ucfirst()->toString())->all()),
                    TextInput::make('nombre')->required()->maxLength(255)->placeholder('Iberia'),
                    TextInput::make('zona')->label('Por dónde va')->maxLength(255)->columnSpanFull()
                        ->placeholder('Vuela a Jerez, Sevilla y Málaga')
                        ->helperText('Sale debajo del nombre, tal cual lo escribas.'),
                    Toggle::make('vuela_a_jerez')->label('Vuela a Jerez')->visible($esAvion)
                        ->helperText('Para el botón «Vuelan a Jerez» de la web.'),
                    Select::make('en_cabina')->label('¿Admite mascotas en cabina?')->visible($esAvion)
                        ->options([1 => 'Sí', 0 => 'No'])->placeholder('Aún no lo sabemos')
                        ->helperText('Para el botón «Admiten en cabina». Si no lo sabes, déjalo vacío.'),
                ]),
            Section::make('Condiciones para mascotas')
                ->icon('heroicon-o-heart')
                ->description('Escríbelo como lo leería alguien con prisa. Lo que no sepas, déjalo vacío: no sale.')
                ->columns(2)
                ->columnSpanFull()
                ->schema([
                    Select::make('estado')->label('Respuesta')->required()->default('no_consta')
                        ->options(Transporte::ESTADOS)
                        ->helperText('Elige el color de la etiqueta: verde, naranja, rojo o gris.'),
                    TextInput::make('respuesta')->label('Texto de la etiqueta')->maxLength(60)
                        ->placeholder('En cabina y en bodega')
                        ->helperText('Opcional. Si lo dejas vacío, sale el texto de la respuesta.'),
                    TextInput::make('peso')->label('Peso máximo')->maxLength(255)->placeholder('8 kg con transportín'),
                    TextInput::make('transportin')->label('Transportín')->maxLength(255)->placeholder('45 × 35 × 25 cm'),
                    TextInput::make('precio')->maxLength(255)->columnSpanFull()->placeholder('40 € por trayecto en España'),
                    Textarea::make('otras_condiciones')->label('Más información')->rows(3)->columnSpanFull()
                        ->helperText('Qué animales admite, cuántas plazas hay, cómo se reserva…'),
                ]),
            Section::make('Comprobación')
                ->icon('heroicon-o-check-badge')
                ->description('De dónde sale el dato y cuándo lo miraste.')
                ->columns(2)
                ->columnSpanFull()
                ->schema([
                    TextInput::make('enlace')->label('Enlace a su norma oficial')->url()->maxLength(500)->columnSpanFull()
                        ->helperText('La página de la compañía donde lo explica. Sale como «Ver su norma».'),
                    DatePicker::make('fecha_comprobacion')->label('Fecha de comprobación')
                        ->helperText('Sale como «Comprobado el…». Si lo dejas vacío, sale «Pendiente de comprobar».'),
                    TextInput::make('orden')->label('Posición')->numeric()->minValue(0)->default(100)
                        ->helperText('Los números más bajos salen antes.'),
                    Toggle::make('publicado')->label('Se ve en la web')->default(true),
                ]),
        ]);
    }
}
