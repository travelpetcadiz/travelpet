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
