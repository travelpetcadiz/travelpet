<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Str;

#[Fillable(['nombre', 'slug', 'comunidad', 'pais', 'latitud', 'longitud', 'zoom', 'activa'])]
class Provincia extends Model
{
    protected $attributes = [
        'pais' => 'ES',
        'zoom' => 9,
        'activa' => true,
    ];

    protected function casts(): array
    {
        return [
            'latitud' => 'float',
            'longitud' => 'float',
            'activa' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Provincia $p) {
            $p->slug = $p->slug ?: Str::slug($p->nombre);
        });
    }

    public function scopeActivas(Builder $q): Builder
    {
        return $q->where('activa', true);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function municipios(): HasMany
    {
        return $this->hasMany(Municipio::class);
    }

    public function sitios(): HasManyThrough
    {
        return $this->hasManyThrough(Sitio::class, Municipio::class);
    }
}
