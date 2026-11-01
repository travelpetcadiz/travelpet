<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable([
    'municipio_id', 'nombre', 'slug', 'tipo', 'subtipo', 'direccion', 'latitud', 'longitud',
    'contacto', 'admite_perros', 'tamano_maximo', 'suplemento', 'zonas_permitidas', 'temporada',
    'otras_condiciones', 'fuente', 'fecha_comprobacion', 'verificado', 'fuente_oficial', 'publicado',
])]
class Sitio extends Model
{
    // Igual que los valores por defecto de la tabla, para que un modelo recién creado los tenga
    protected $attributes = [
        'admite_perros' => 'no_consta',
        'verificado' => false,
        'fuente_oficial' => false,
        'publicado' => true,
    ];

    // tipo => [etiqueta pública, color]
    public const TIPOS = [
        'Alojamiento' => ['Alojamiento', '#2F6F8F'],
        'Restauración' => ['Restauración', '#C0582F'],
        'Playa' => ['Playa', '#1F8A8A'],
        'Espacio' => ['Parques y espacios', '#5B8C2A'],
        'Salud' => ['Veterinarios', '#B3261E'],
        'Servicios' => ['Servicios', '#7B5AA6'],
        'Transporte y ocio' => ['Transporte y ocio', '#6B6B3A'],
    ];

    public const ADMITE = [
        'si' => 'Sí',
        'condiciones' => 'Con condiciones',
        'no' => 'No',
        'no_consta' => 'No consta',
    ];

    // Datos con más de 6 meses se marcan como antiguos
    public const DIAS_ANTIGUO = 183;

    protected function casts(): array
    {
        return [
            'latitud' => 'float',
            'longitud' => 'float',
            'fecha_comprobacion' => 'date',
            'verificado' => 'boolean',
            'fuente_oficial' => 'boolean',
            'publicado' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Sitio $sitio) {
            $sitio->slug ??= static::slugUnico($sitio->nombre.' '.$sitio->municipio?->nombre);
        });

        // Cada cambio hecho desde el panel queda en el historial
        static::updated(function (Sitio $sitio) {
            foreach ($sitio->getChanges() as $campo => $nuevo) {
                if (in_array($campo, ['updated_at', 'slug'], true)) {
                    continue;
                }
                Cambio::create([
                    'sitio_id' => $sitio->id,
                    'user_id' => auth()->id(),
                    'sitio_nombre' => $sitio->nombre,
                    'municipio' => $sitio->municipio?->nombre,
                    'campo' => $campo,
                    'antes' => static::texto($sitio->getOriginal($campo)),
                    'despues' => static::texto($nuevo),
                    'fuente' => auth()->user()?->name ?? 'Sistema',
                    'fecha' => now()->toDateString(),
                ]);
            }
        });
    }

    public static function slugUnico(string $texto): string
    {
        $base = Str::slug($texto);
        $slug = $base;
        for ($i = 2; static::where('slug', $slug)->exists(); $i++) {
            $slug = "{$base}-{$i}";
        }

        return $slug;
    }

    private static function texto(mixed $v): ?string
    {
        return match (true) {
            $v instanceof \DateTimeInterface => $v->format('Y-m-d'),
            is_bool($v) => $v ? 'Sí' : 'No',
            default => $v === null ? null : (string) $v,
        };
    }

    // Publicados y de una provincia activa
    public function scopePublicados(Builder $q): Builder
    {
        return $q->where('publicado', true)
            ->whereHas('municipio.provincia', fn (Builder $p) => $p->where('activa', true));
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function etiquetaTipo(): string
    {
        return static::TIPOS[$this->tipo][0] ?? $this->tipo;
    }

    public function color(): string
    {
        return static::TIPOS[$this->tipo][1] ?? '#56696D';
    }

    public function etiquetaAdmite(): string
    {
        return static::ADMITE[$this->admite_perros] ?? $this->admite_perros;
    }

    public function esAntiguo(): bool
    {
        return ! $this->fecha_comprobacion || $this->fecha_comprobacion->diffInDays(now()) > static::DIAS_ANTIGUO;
    }

    public function municipio(): BelongsTo
    {
        return $this->belongsTo(Municipio::class);
    }

    public function resenas(): HasMany
    {
        return $this->hasMany(Resena::class);
    }

    public function cambios(): HasMany
    {
        return $this->hasMany(Cambio::class);
    }
}
