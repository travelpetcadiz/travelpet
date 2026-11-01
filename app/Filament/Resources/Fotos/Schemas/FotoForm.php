<?php

namespace App\Filament\Resources\Fotos\Schemas;

use App\Models\Foto;
use App\Models\Sitio;
use App\Support\Wikimedia;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class FotoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components(static::campos(conSitio: true));
    }

    /**
     * Campos de una foto. También se usan dentro de la ficha de un sitio (sin elegir el sitio).
     */
    public static function campos(bool $conSitio): array
    {
        $esWikimedia = fn (Get $get) => $get('origen') === 'wikimedia';

        return array_values(array_filter([
            Section::make('La foto')
                ->icon('heroicon-o-photo')
                ->columnSpanFull()
                ->schema([
                    Radio::make('origen')
                        ->label('¿De dónde sale?')
                        ->options(Foto::ORIGENES)
                        ->descriptions([
                            'propia' => 'Es tuya. La puedes usar sin más.',
                            'cedida' => 'Te la ha mandado el hotel, el bar… con permiso para usarla. Guarda el correo.',
                            'wikimedia' => 'Una foto libre de commons.wikimedia.org. Pega su enlace y se rellena todo solo.',
                        ])
                        ->default('propia')
                        ->required()
                        ->live(),
                    TextInput::make('fuente_url')
                        ->label('Enlace de la foto en Wikimedia Commons')
                        ->placeholder('https://commons.wikimedia.org/wiki/File:…')
                        ->url()
                        ->helperText('Abre la foto en commons.wikimedia.org y copia la dirección de su página. El autor y la licencia se rellenan solos al salir de este campo.')
                        ->visible($esWikimedia)
                        ->required($esWikimedia)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (?string $state, Set $set, Get $get) {
                            if ($state && ($datos = Wikimedia::datos($state))) {
                                $set('autor', $datos['autor']);
                                $set('licencia', $datos['licencia']);
                                $set('licencia_url', $datos['licencia_url']);
                                if (blank($get('descripcion'))) {
                                    $set('descripcion', $datos['titulo']);
                                }
                            }
                        }),
                    FileUpload::make('archivo')
                        ->label('Foto')
                        ->disk('public')
                        ->directory('fotos')
                        ->image()
                        ->maxSize(12 * 1024)
                        ->required(fn (Get $get) => $get('origen') !== 'wikimedia')
                        ->visible(fn (Get $get, ?Foto $record) => $get('origen') !== 'wikimedia' || $record?->archivo)
                        ->helperText('Arrastra aquí la foto o pulsa para elegirla (hasta 12 MB). Mejor en horizontal y con buena luz. Al guardarla se le quita la ubicación GPS y se hace más ligera.'),
                    TextInput::make('descripcion')
                        ->label('Qué se ve')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('La playa de Bateles al atardecer, con la torre de Guzmán al fondo')
                        ->helperText('Una frase sencilla. La leen las personas ciegas con su lector de pantalla, y también Google.'),
                ]),

            $conSitio ? Section::make('Dónde sale')
                ->icon('heroicon-o-map-pin')
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    Select::make('sitio_id')
                        ->label('Sitio')
                        ->relationship('sitio', 'nombre')
                        ->getOptionLabelFromRecordUsing(fn (Sitio $s) => $s->nombre.' ('.$s->municipio?->nombre.')')
                        ->searchable()
                        ->placeholder('Ninguno: es para el diseño de la web')
                        ->helperText('Sale en la ficha de ese sitio. Si la dejas vacía, es para el diseño de la web (portada…).')
                        ->live(),
                    Toggle::make('para_portada')
                        ->label('Me gustaría que saliera en la portada')
                        ->visible(fn (Get $get) => blank($get('sitio_id'))),
                ]) : null,

            Section::make('Permiso y crédito')
                ->description('Debajo de la foto saldrá quién la hizo. Las licencias libres obligan a citarlo.')
                ->icon('heroicon-o-shield-check')
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    TextInput::make('autor')
                        ->label(fn (Get $get) => match ($get('origen')) {
                            'cedida' => 'Quién la cede (nombre del sitio)',
                            'wikimedia' => 'Autor (se rellena solo)',
                            default => 'Quién la hizo',
                        })
                        ->default(fn () => auth()->user()?->name)
                        ->required(),
                    TextInput::make('lugar')->label('Dónde está hecha')->placeholder('Conil de la Frontera'),
                    TextInput::make('licencia')->label('Licencia (se rellena sola)')->readOnly()->visible($esWikimedia),
                    TextInput::make('licencia_url')->label('Enlace a la licencia')->readOnly()->visible($esWikimedia),
                    Textarea::make('permiso')
                        ->label('Cómo te dio permiso')
                        ->placeholder('Correo de Laura (recepción) del 3/11/2026: «Podéis usar estas fotos en TravelPet»')
                        ->helperText('Para saber, si algún día lo preguntan, que la foto se puede usar.')
                        ->rows(2)
                        ->columnSpanFull()
                        ->visible(fn (Get $get) => $get('origen') === 'cedida')
                        ->required(fn (Get $get) => $get('origen') === 'cedida'),
                ]),

            Section::make('Antes de publicarla')
                ->icon('heroicon-o-eye')
                ->columnSpanFull()
                ->schema([
                    Checkbox::make('sin_personas')
                        ->label('He mirado la foto: no sale ninguna persona que se pueda reconocer (ni niños)')
                        ->helperText('Si sale alguien de cerca, haría falta su permiso. Mejor otra foto.')
                        ->live(),
                    Toggle::make('publicada')
                        ->label('Se ve en la web')
                        ->helperText(fn (Get $get) => $get('sin_personas')
                            ? 'Si está apagado, la foto se guarda pero no sale en la web.'
                            : 'Para publicarla, marca primero la casilla de arriba.')
                        ->disabled(fn (Get $get) => ! $get('sin_personas'))
                        // Sin la comprobación de personas, nunca se publica
                        ->dehydrateStateUsing(fn ($state, Get $get) => (bool) $state && (bool) $get('sin_personas')),
                ]),
        ]));
    }
}
