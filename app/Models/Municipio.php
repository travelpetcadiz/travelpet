<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable(['provincia_id', 'nombre', 'slug', 'comarca', 'latitud', 'longitud'])]
class Municipio extends Model
{
    protected function casts(): array
    {
        return [
            'latitud' => 'float',
            'longitud' => 'float',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Municipio $m) {
            $m->slug = $m->slug ?: Str::slug($m->nombre);
        });
    }

    public function provincia(): BelongsTo
    {
        return $this->belongsTo(Provincia::class);
    }

    public function sitios(): HasMany
    {
        return $this->hasMany(Sitio::class);
    }

    // "Conil de la Frontera (Cádiz)", para selects con varias provincias
    public function nombreCompleto(): string
    {
        return $this->nombre.' ('.$this->provincia->nombre.')';
    }
}
