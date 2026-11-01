<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['sitio_id', 'user_id', 'estrellas', 'pudo_entrar', 'tamano_perro', 'texto', 'estado'])]
class Resena extends Model
{
    public const ESTADOS = [
        'pendiente' => 'Por revisar',
        'aprobada' => 'Publicada',
        'rechazada' => 'No publicada',
    ];

    public const TAMANOS = [
        'pequeno' => 'Pequeño (hasta 10 kg)',
        'mediano' => 'Mediano (10 a 25 kg)',
        'grande' => 'Grande (más de 25 kg)',
    ];

    public function scopeAprobadas(Builder $q): Builder
    {
        return $q->where('estado', 'aprobada');
    }

    public function sitio(): BelongsTo
    {
        return $this->belongsTo(Sitio::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
