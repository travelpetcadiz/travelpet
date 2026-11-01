<?php

namespace App\Filament\Resources\Sitios\Schemas;

use App\Filament\Resources\Municipios\Schemas\MunicipioForm;
use App\Models\Municipio;
use App\Models\Sitio;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class SitioForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Datos básicos')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('nombre')->required()->maxLength(255),
                        Select::make('tipo')
                            ->options(collect(Sitio::TIPOS)->mapWithKeys(fn ($v, $k) => [$k => $v[0]]))
                            ->required(),
                        TextInput::make('subtipo')->placeholder('Hotel, playa canina, clínica…'),
                        Select::make('municipio_id')
                            ->label('Municipio')
                            ->relationship('municipio', 'nombre', fn (Builder $query) => $query->with('provincia')->orderBy('nombre'))
                            ->getOptionLabelFromRecordUsing(fn (Municipio $m) => $m->nombreCompleto())
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm(MunicipioForm::campos()),
                        TextInput::make('direccion')->label('Dirección'),
                        TextInput::make('latitud')->numeric()->minValue(-90)->maxValue(90)->helperText('Ejemplo: 36.529800. En Google Maps: clic derecho sobre el sitio.'),
                        TextInput::make('longitud')->numeric()->minValue(-180)->maxValue(180)->helperText('Ejemplo: -6.292600'),
                        Textarea::make('contacto')
                            ->label('Teléfono / Web')
                            ->helperText('Separa varios con punto y coma. Las direcciones que empiezan por https:// salen como enlace.')
                            ->columnSpanFull(),
                    ]),
                Section::make('Condiciones para mascotas')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Select::make('admite_perros')->label('¿Admite mascotas?')->options(Sitio::ADMITE)->required()->default('no_consta'),
                        TextInput::make('tamano_maximo')->label('Tamaño o peso máximo'),
                        TextInput::make('suplemento')->label('Suplemento (€)'),
                        TextInput::make('temporada'),
                        Textarea::make('zonas_permitidas')->rows(3)->columnSpanFull(),
                        Textarea::make('otras_condiciones')->rows(3)->columnSpanFull(),
                    ]),
                Section::make('Para los filtros')
                    ->description('Lo que usan los filtros de la web y los iconos de la ficha. Si no lo sabes seguro, déjalo en «No lo sabemos»: así no sale en un filtro por error.')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Select::make('admite_grandes')->label('¿Admite perros grandes (más de 25 kg)?')
                            ->options(['si' => 'Sí', 'no' => 'No'])
                            ->placeholder('No lo sabemos')
                            ->formatStateUsing(fn ($state) => $state === null ? null : ($state ? 'si' : 'no'))
                            ->dehydrateStateUsing(fn ($state) => blank($state) ? null : $state === 'si'),
                        Select::make('sin_suplemento')->label('¿Es gratis (sin suplemento por la mascota)?')
                            ->options(['si' => 'Sí', 'no' => 'No'])
                            ->placeholder('No lo sabemos')
                            ->formatStateUsing(fn ($state) => $state === null ? null : ($state ? 'si' : 'no'))
                            ->dehydrateStateUsing(fn ($state) => blank($state) ? null : $state === 'si'),
                        Select::make('puede_entrar')->label('¿Puede estar dentro del local? (bares y restaurantes)')
                            ->options(['si' => 'Sí', 'no' => 'No'])
                            ->placeholder('No lo sabemos')
                            ->formatStateUsing(fn ($state) => $state === null ? null : ($state ? 'si' : 'no'))
                            ->dehydrateStateUsing(fn ($state) => blank($state) ? null : $state === 'si'),
                        Select::make('sin_correa')->label('¿Hay zona para ir sin correa? (parques y playas)')
                            ->options(['si' => 'Sí', 'no' => 'No'])
                            ->placeholder('No lo sabemos')
                            ->formatStateUsing(fn ($state) => $state === null ? null : ($state ? 'si' : 'no'))
                            ->dehydrateStateUsing(fn ($state) => blank($state) ? null : $state === 'si'),
                        Toggle::make('urgencias_24h')->label('Urgencias 24 h (veterinarios)')
                            ->helperText('Sale en «Urgencias veterinarias 24 h» de la portada.'),
                    ]),
                Section::make('Fechas en que se puede ir (playas)')
                    ->description('Con esto la ficha dice «Hoy se puede ir» o «Hoy no». Los días van como día/mes: 01/06 es el 1 de junio.')
                    ->columnSpanFull()
                    ->columns(3)
                    ->schema([
                        Select::make('periodo_tipo')->label('Cuándo')
                            ->options(Sitio::PERIODOS)
                            ->placeholder('No lo sabemos')
                            ->live(),
                        TextInput::make('periodo_desde')->label('Desde (día/mes)')
                            ->placeholder('01/06')
                            ->regex('#^(\d{2}/\d{2}|ramos)$#')
                            ->validationMessages(['regex' => 'Escribe el día y el mes así: 01/06. Para el Domingo de Ramos, escribe ramos.'])
                            ->helperText('Para el Domingo de Ramos, escribe «ramos».')
                            ->required(fn ($get) => in_array($get('periodo_tipo'), ['solo', 'prohibido'], true))
                            ->visible(fn ($get) => in_array($get('periodo_tipo'), ['solo', 'prohibido'], true)),
                        TextInput::make('periodo_hasta')->label('Hasta (día/mes)')
                            ->placeholder('30/09')
                            ->regex('#^\d{2}/\d{2}$#')
                            ->validationMessages(['regex' => 'Escribe el día y el mes así: 30/09.'])
                            ->required(fn ($get) => in_array($get('periodo_tipo'), ['solo', 'prohibido'], true))
                            ->visible(fn ($get) => in_array($get('periodo_tipo'), ['solo', 'prohibido'], true)),
                    ]),
                Section::make('Comprobación')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Textarea::make('fuente')->rows(2)->columnSpanFull()->helperText('De dónde sale el dato: web oficial, email, llamada…'),
                        DatePicker::make('fecha_comprobacion')->label('Fecha de comprobación')->default(now()),
                        Toggle::make('verificado')->helperText('Confirmado directamente con el sitio'),
                        Toggle::make('fuente_oficial')->label('Fuente oficial'),
                        Toggle::make('publicado')->default(true)->helperText('Si lo desactivas, deja de verse en la web'),
                    ]),
            ]);
    }
}
