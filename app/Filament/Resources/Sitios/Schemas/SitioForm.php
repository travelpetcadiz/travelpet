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
use Filament\Schemas\Components\Utilities\Get;
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
                    ->description('Qué es, dónde está y cómo contactar.')
                    ->icon('heroicon-o-map-pin')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('nombre')->required()->maxLength(255),
                        Select::make('tipo')
                            ->options(collect(Sitio::TIPOS)->mapWithKeys(fn ($v, $k) => [$k => $v[0]]))
                            ->live()
                            ->required(),
                        TextInput::make('subtipo')->label('Qué es exactamente')->placeholder('Hotel, playa canina, clínica…')
                            ->helperText('Sale debajo del nombre en la web.'),
                        Select::make('municipio_id')
                            ->label('Municipio')
                            ->relationship('municipio', 'nombre', fn (Builder $query) => $query->with('provincia')->orderBy('nombre'))
                            ->getOptionLabelFromRecordUsing(fn (Municipio $m) => $m->nombreCompleto())
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm(MunicipioForm::campos()),
                        TextInput::make('direccion')->label('Dirección'),
                        Toggle::make('a_domicilio')->label('Servicio a domicilio (no tiene local)')
                            ->live()
                            ->helperText('Para peluquerías, veterinarios o paseadores que van a casa. No sale en el mapa y no hace falta ubicación.')
                            ->columnSpanFull(),
                        TextInput::make('latitud')->label('Ubicación: latitud')->numeric()->minValue(-90)->maxValue(90)
                            ->placeholder('36.529800')
                            ->helperText('En Google Maps, pulsa con el botón derecho sobre el sitio y copia los dos números: el primero va aquí.')
                            ->hidden(fn (Get $get) => $get('a_domicilio')),
                        TextInput::make('longitud')->label('Ubicación: longitud')->numeric()->minValue(-180)->maxValue(180)
                            ->placeholder('-6.292600')
                            ->helperText('El segundo número (en Cádiz empieza por -5 o -6). Sin ubicación, el sitio no sale en el mapa.')
                            ->hidden(fn (Get $get) => $get('a_domicilio')),
                        Textarea::make('contacto')
                            ->label('Teléfono / Web')
                            ->helperText('Separa varios con punto y coma. Las direcciones que empiezan por https:// salen como enlace.')
                            ->columnSpanFull(),
                        TextInput::make('enlace_reserva')
                            ->label('Enlace para reservar (afiliado)')
                            ->helperText('Solo alojamientos. Pega aquí el enlace de Booking que te da Travelpayouts para este alojamiento. En la ficha sale el botón «Ver precios y reservar» con un aviso de que es un enlace de afiliado. Si lo dejas vacío, no sale el botón.')
                            ->url()
                            ->maxLength(1000)
                            ->visible(fn (Get $get) => $get('tipo') === 'Alojamiento')
                            ->columnSpanFull(),
                    ]),
                Section::make('Condiciones para mascotas')
                    ->description('Lo que la gente necesita saber antes de ir. Si algo no lo sabes, déjalo vacío: la web dirá «Aún no sabemos».')
                    ->icon('heroicon-o-check-circle')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Select::make('admite_perros')->label('¿Admite mascotas?')->options(Sitio::ADMITE)->required()->default('no_consta'),
                        TextInput::make('tamano_maximo')->label('Tamaño o peso máximo'),
                        TextInput::make('suplemento')->label('Suplemento (€)'),
                        TextInput::make('temporada')->label('Temporada u horario')
                            ->helperText('En veterinarios y servicios, su horario. En playas, el texto de la temporada (las fechas exactas van más abajo).'),
                        Textarea::make('zonas_permitidas')->rows(3)->columnSpanFull(),
                        Textarea::make('otras_condiciones')->label('Información adicional')->rows(3)->columnSpanFull()
                            ->helperText('Cualquier otra cosa útil: normas, documentación que piden, si tienen agua para mascotas…'),
                    ]),
                Section::make('Para los filtros')
                    ->icon('heroicon-o-adjustments-horizontal')
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
                    ->icon('heroicon-o-calendar-days')
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
                    ->description('De dónde sale el dato y si se ve en la web. Es lo que da confianza a la guía.')
                    ->icon('heroicon-o-shield-check')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Textarea::make('fuente')->label('De dónde sale el dato')->rows(2)->columnSpanFull()
                            ->placeholder('Llamada al hotel el 2/11/2026 · web del ayuntamiento · email…'),
                        DatePicker::make('fecha_comprobacion')->label('Cuándo lo comprobaste')->default(now())
                            ->native(false)->displayFormat('d/m/Y')->firstDayOfWeek(1)
                            ->helperText('A los 6 meses la web avisa de que el dato puede estar antiguo.'),
                        Toggle::make('verificado')->label('Confirmado por el propio sitio')
                            ->helperText('Márcalo si te lo ha dicho el propio sitio: por teléfono, email o en persona.'),
                        Toggle::make('fuente_oficial')->label('Sale de una fuente oficial')
                            ->helperText('Su propia web, su ayuntamiento o un organismo público.'),
                        Toggle::make('publicado')->label('Se ve en la web')->default(true)
                            ->helperText('Apágalo si ha cerrado o no quieres que se vea. No se borra nada.'),
                    ]),
            ]);
    }
}
