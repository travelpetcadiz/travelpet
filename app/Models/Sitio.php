<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

#[Fillable([
    'municipio_id', 'nombre', 'slug', 'tipo', 'subtipo', 'direccion', 'latitud', 'longitud', 'a_domicilio',
    'contacto', 'admite_perros', 'tamano_maximo', 'suplemento', 'zonas_permitidas', 'temporada',
    'otras_condiciones', 'fuente', 'fecha_comprobacion', 'verificado', 'fuente_oficial', 'publicado',
    'admite_grandes', 'sin_suplemento', 'puede_entrar', 'sin_correa', 'urgencias_24h',
    'periodo_tipo', 'periodo_desde', 'periodo_hasta',
])]
class Sitio extends Model
{
    // Igual que los valores por defecto de la tabla, para que un modelo recién creado los tenga
    protected $attributes = [
        'admite_perros' => 'no_consta',
        'verificado' => false,
        'fuente_oficial' => false,
        'publicado' => true,
        'urgencias_24h' => false,
        'a_domicilio' => false,
    ];

    // Nombre de cada campo tal como lo entiende una persona (para el historial)
    public const CAMPOS = [
        'nombre' => 'Nombre', 'tipo' => 'Tipo', 'subtipo' => 'Qué es exactamente', 'municipio_id' => 'Municipio',
        'direccion' => 'Dirección', 'latitud' => 'Ubicación (latitud)', 'longitud' => 'Ubicación (longitud)', 'a_domicilio' => 'Servicio a domicilio',
        'contacto' => 'Teléfono / web', 'admite_perros' => '¿Admite mascotas?', 'tamano_maximo' => 'Tamaño o peso máximo',
        'suplemento' => 'Suplemento', 'zonas_permitidas' => 'Zonas permitidas', 'temporada' => 'Temporada / horario',
        'otras_condiciones' => 'Información adicional', 'fuente' => 'De dónde sale el dato', 'fecha_comprobacion' => 'Fecha de comprobación',
        'verificado' => 'Confirmado por el propio sitio', 'fuente_oficial' => 'Fuente oficial', 'publicado' => 'Se ve en la web',
        'admite_grandes' => 'Admite perros grandes', 'sin_suplemento' => 'Sin suplemento', 'puede_entrar' => 'Puede estar dentro',
        'sin_correa' => 'Zona sin correa', 'urgencias_24h' => 'Urgencias 24 h', 'periodo_tipo' => 'Fechas: cuándo',
        'periodo_desde' => 'Fechas: desde', 'periodo_hasta' => 'Fechas: hasta',
        'Latitud/Longitud' => 'Ubicación',
    ];

    public const PERIODOS = [
        'todo' => 'Se puede ir todo el año',
        'solo' => 'Solo se puede ir entre estas fechas',
        'prohibido' => 'No se puede ir entre estas fechas',
    ];

    // tipo => [nombre que ve la gente (también en el panel), color, icono de Heroicons]
    public const TIPOS = [
        'Alojamiento' => ['Dormir', '#2F6F8F', 'home-modern'],
        'Restauración' => ['Comer y beber', '#C0582F', 'building-storefront'],
        'Playa' => ['Playas', '#1F8A8A', 'sun'],
        'Espacio' => ['Parques y paseos', '#5B8C2A', 'map'],
        'Salud' => ['Veterinarios', '#B3261E', 'heart'],
        'Servicios' => ['Servicios para mascotas', '#7B5AA6', 'scissors'],
        'Ocio' => ['Planes', '#6B6B3A', 'ticket'],
        'Transporte' => ['Transporte', '#4B5D73', 'truck'],
        'Comercio' => ['Comercios', '#9C4F6E', 'shopping-bag'],
    ];

    // Lo que busca quien viaja: van como accesos grandes en la portada; el resto, como utilidades
    public const PRINCIPALES = ['Alojamiento', 'Restauración', 'Playa', 'Espacio'];

    // Sitios para mascotas (veterinarios, peluquerías…): no tiene sentido decir si "admiten mascotas"
    public const SERVICIOS = ['Salud', 'Servicios'];

    public const ADMITE = [
        'si' => 'Sí',
        'condiciones' => 'Con condiciones',
        'no' => 'No',
        'no_consta' => 'No consta',
    ];

    // Datos con más de 6 meses se marcan como antiguos
    public const DIAS_ANTIGUO = 183;

    // Quién figura en el historial cuando el cambio no lo hace una persona del panel
    public static string $origenCambios = 'Sistema';

    protected function casts(): array
    {
        return [
            'latitud' => 'float',
            'longitud' => 'float',
            'fecha_comprobacion' => 'date',
            'verificado' => 'boolean',
            'fuente_oficial' => 'boolean',
            'publicado' => 'boolean',
            'admite_grandes' => 'boolean',
            'sin_suplemento' => 'boolean',
            'puede_entrar' => 'boolean',
            'sin_correa' => 'boolean',
            'urgencias_24h' => 'boolean',
            'a_domicilio' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Sitio $sitio) {
            // Para el slug hace falta el nombre del municipio
            $sitio->loadMissing('municipio');
            $sitio->slug ??= static::slugUnico($sitio->nombre.' '.$sitio->municipio?->nombre);
        });

        // Un servicio a domicilio no tiene local: sin ubicación, para que no salga en el mapa
        static::saving(function (Sitio $sitio) {
            if ($sitio->a_domicilio) {
                $sitio->latitud = null;
                $sitio->longitud = null;
            }
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
                    'fuente' => auth()->user()?->name ?? static::$origenCambios,
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
    // Les falta la latitud o la longitud y deberían tenerla (los servicios a domicilio no la llevan)
    public function scopeSinUbicacion(Builder $q): Builder
    {
        return $q->where('a_domicilio', false)
            ->where(fn (Builder $w) => $w->whereNull('latitud')->orWhereNull('longitud'));
    }

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

    public function icono(): string
    {
        return static::TIPOS[$this->tipo][2] ?? 'map-pin';
    }

    // Primer teléfono español del campo de contacto, en formato +34XXXXXXXXX (para llamar y para Google)
    public function telefono(): ?string
    {
        return preg_match('/(?:\+34[\s.]?)?([6789](?:[\s.]?\d){8})/', (string) $this->contacto, $m)
            ? '+34'.preg_replace('/\D/', '', $m[1])
            : null;
    }

    // En veterinarios con urgencias, el número de urgencias si el texto lo indica («Urgencias 24 h: 722 200 044»)
    public function telefonoUrgencias(): ?string
    {
        $texto = $this->otras_condiciones.' '.$this->temporada;

        return preg_match('/urgencias(?:\s*24\s*h(?:oras)?)?[^0-9]{0,20}((?:\+34[\s.]?)?[6789](?:[\s.]?\d){8})/iu', $texto, $m)
            ? '+34'.preg_replace('/\D/', '', preg_replace('/^\+34/', '', $m[1]))
            : $this->telefono();
    }

    public function esServicio(): bool
    {
        return in_array($this->tipo, static::SERVICIOS, true);
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

    /**
     * ¿Se puede ir hoy? Solo para sitios con periodo (sobre todo playas).
     *
     * @return array{puede: bool, texto: string}|null
     */
    public function hoy(?CarbonInterface $hoy = null): ?array
    {
        if (! $this->periodo_tipo || $this->admite_perros === 'no') {
            return null;
        }
        if ($this->periodo_tipo === 'todo') {
            return ['puede' => true, 'texto' => 'Se puede ir todo el año.'];
        }

        $hoy = ($hoy ?? now('Europe/Madrid'))->copy()->startOfDay();
        $desde = static::fechaDelAno($this->periodo_desde, $hoy->year);
        $hasta = static::fechaDelAno($this->periodo_hasta, $hoy->year);
        if (! $desde || ! $hasta) {
            return null;
        }

        // Periodos que cruzan el año (15/10 al 15/03)
        $dentro = $desde->lte($hasta)
            ? $hoy->betweenIncluded($desde, $hasta)
            : $hoy->gte($desde) || $hoy->lte($hasta);
        $dia = fn (CarbonInterface $f) => $f->translatedFormat('j \d\e F');

        // Si el periodo de este año ya pasó, se habla del siguiente (el Domingo de Ramos cambia de fecha)
        if (! $dentro && $desde->lte($hasta) && $hoy->gt($hasta)) {
            $desde = static::fechaDelAno($this->periodo_desde, $hoy->year + 1);
            $hasta = static::fechaDelAno($this->periodo_hasta, $hoy->year + 1);
        }

        if ($this->periodo_tipo === 'prohibido') {
            return $dentro
                ? ['puede' => false, 'texto' => 'No se puede ir hasta el '.$dia($hasta->copy()->addDay()).'.']
                : ['puede' => true, 'texto' => 'Prohibido del '.$dia($desde).' al '.$dia($hasta).'.'];
        }

        return $dentro
            ? ['puede' => true, 'texto' => 'Abierto hasta el '.$dia($hasta).'.']
            : ['puede' => false, 'texto' => 'Se puede ir a partir del '.$dia($desde).'.'];
    }

    // «01/06» o «ramos» (Domingo de Ramos) en un año concreto
    public static function fechaDelAno(?string $valor, int $ano): ?CarbonInterface
    {
        if ($valor === 'ramos') {
            return static::domingoDeRamos($ano);
        }
        if (! $valor || ! preg_match('#^(\d{2})/(\d{2})$#', $valor, $m) || ! checkdate((int) $m[2], (int) $m[1], $ano)) {
            return null;
        }

        return Carbon::create($ano, (int) $m[2], (int) $m[1], 0, 0, 0, 'Europe/Madrid');
    }

    // Domingo de Pascua (algoritmo de Meeus) menos una semana
    public static function domingoDeRamos(int $ano): CarbonInterface
    {
        $a = $ano % 19;
        $b = intdiv($ano, 100);
        $c = $ano % 100;
        $h = (19 * $a + $b - intdiv($b, 4) - intdiv($b - intdiv($b + 8, 25) + 1, 3) + 15) % 30;
        $l = (32 + 2 * ($b % 4) + 2 * intdiv($c, 4) - $h - $c % 4) % 7;
        $m = intdiv($a + 11 * $h + 22 * $l, 451);
        $mes = intdiv($h + $l - 7 * $m + 114, 31);
        $dia = ($h + $l - 7 * $m + 114) % 31 + 1;

        return Carbon::create($ano, $mes, $dia, 0, 0, 0, 'Europe/Madrid')->subWeek();
    }

    public function fotos(): HasMany
    {
        return $this->hasMany(Foto::class)->orderBy('orden')->orderBy('id');
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
