<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'sitio_id', 'provincia_id', 'tipo', 'nombre', 'municipio', 'mensaje', 'estado'])]
class Sugerencia extends Model
{
    public const TIPOS = [
        'nuevo_sitio' => 'Sitio nuevo',
        'error' => 'Dato incorrecto',
    ];

    public const ESTADOS = [
        'pendiente' => 'Pendiente',
        'hecha' => 'Hecha',
        'descartada' => 'Descartada',
    ];

    public function sitio(): BelongsTo
    {
        return $this->belongsTo(Sitio::class);
    }

    public function provincia(): BelongsTo
    {
        return $this->belongsTo(Provincia::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
