<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'medio', 'nombre', 'zona', 'vuela_a_jerez', 'en_cabina', 'estado', 'respuesta', 'peso', 'transportin',
    'precio', 'otras_condiciones', 'enlace', 'fecha_comprobacion', 'orden', 'publicado',
])]
class Transporte extends Model
{
    protected $attributes = [
        'estado' => 'no_consta',
        'vuela_a_jerez' => false,
        'orden' => 100,
        'publicado' => true,
    ];

    /** @var array<string, array{0: string, 1: string}> medio => [título de la sección, icono de Heroicons] */
    public const MEDIOS = [
        'avion' => ['En avión', 'paper-airplane'],
        'tren' => ['En tren', 'ticket'],
        'autobus' => ['En autobús', 'truck'],
        'barco' => ['En barco', 'globe-europe-africa'],
    ];

    /** @var array<string, string> estado => respuesta por defecto */
    public const ESTADOS = [
        'si' => 'Admite mascotas',
        'condiciones' => 'Con condiciones',
        'no' => 'No admite mascotas',
        'no_consta' => 'Aún no lo hemos comprobado',
    ];

    protected function casts(): array
    {
        return [
            'vuela_a_jerez' => 'boolean',
            'en_cabina' => 'boolean',
            'publicado' => 'boolean',
            'fecha_comprobacion' => 'date',
            'orden' => 'integer',
        ];
    }

    public function scopePublicados(Builder $query): Builder
    {
        return $query->where('publicado', true);
    }

    /** Lo que sale en la etiqueta de color: el texto propio o el del estado */
    public function textoRespuesta(): string
    {
        return $this->respuesta ?: self::ESTADOS[$this->estado] ?? self::ESTADOS['no_consta'];
    }
}
