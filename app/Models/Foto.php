<?php

namespace App\Models;

use App\Support\Wikimedia;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Fillable([
    'sitio_id', 'archivo', 'descripcion', 'lugar', 'autor', 'para_portada', 'user_id',
    'origen', 'fuente_url', 'licencia', 'licencia_url', 'permiso', 'sin_personas', 'publicada', 'orden',
])]
class Foto extends Model
{
    public const ORIGENES = [
        'propia' => 'La he hecho yo (o alguien del equipo)',
        'cedida' => 'Me la ha cedido el propio sitio',
        'wikimedia' => 'Wikimedia Commons (licencia libre)',
    ];

    // Tamaño máximo al guardar: suficiente para la ficha y ligera para el móvil
    private const ANCHO_MAXIMO = 1600;

    protected $attributes = [
        'para_portada' => false,
        'origen' => 'propia',
        'sin_personas' => false,
        'publicada' => false,
        'orden' => 0,
    ];

    protected function casts(): array
    {
        return [
            'para_portada' => 'boolean',
            'sin_personas' => 'boolean',
            'publicada' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Foto $foto) {
            // De Wikimedia basta con el enlace: se rellenan solos el autor, la licencia y la imagen
            if ($foto->origen === 'wikimedia' && $foto->fuente_url && (! $foto->archivo || ! $foto->autor || ! $foto->licencia)) {
                if ($datos = Wikimedia::datos($foto->fuente_url)) {
                    $foto->autor ??= $datos['autor'];
                    $foto->licencia ??= $datos['licencia'];
                    $foto->licencia_url ??= $datos['licencia_url'];
                    $foto->descripcion ??= $datos['titulo'];
                    $foto->archivo ??= Wikimedia::descargar($datos['imagen_url']);
                }
            }
            if ($foto->archivo && $foto->isDirty('archivo')) {
                $foto->archivo = static::procesar($foto->archivo);
            }
        });

        // Al borrar la ficha de la foto, se borra también el archivo
        static::deleted(fn (Foto $foto) => $foto->archivo && Storage::disk('public')->delete($foto->archivo));
    }

    /**
     * Endereza la foto, la reduce y la guarda en WebP. Al volver a codificarla se pierden los datos
     * ocultos (EXIF), entre ellos la ubicación GPS de donde se hizo. Sin GD, la deja como está.
     */
    public static function procesar(string $ruta): string
    {
        $disco = Storage::disk('public');
        if (! function_exists('imagecreatefromstring') || ! $disco->exists($ruta) || str_ends_with($ruta, '.webp')) {
            return $ruta;
        }
        $imagen = @imagecreatefromstring($disco->get($ruta));
        if (! $imagen) {
            return $ruta;
        }

        $giro = function_exists('exif_read_data') ? (@exif_read_data($disco->path($ruta))['Orientation'] ?? 1) : 1;
        $imagen = match ((int) $giro) {
            3 => imagerotate($imagen, 180, 0),
            6 => imagerotate($imagen, -90, 0),
            8 => imagerotate($imagen, 90, 0),
            default => $imagen,
        };
        if (imagesx($imagen) > self::ANCHO_MAXIMO) {
            $imagen = imagescale($imagen, self::ANCHO_MAXIMO);
        }

        $nueva = Str::beforeLast($ruta, '.').'.webp';
        ob_start();
        imagewebp($imagen, null, 80);
        $disco->put($nueva, ob_get_clean());
        $disco->delete($ruta);

        return $nueva;
    }

    public function scopePublicadas(Builder $q): Builder
    {
        return $q->where('publicada', true)->orderBy('orden')->orderBy('id');
    }

    /**
     * Fotos del carrusel de la portada: primero las numeradas (1, 2, 3…) y luego las demás, de la más nueva a la más antigua.
     */
    public function scopeParaPortada(Builder $q): Builder
    {
        return $q->where('para_portada', true)->orderByRaw('orden = 0')->orderBy('orden')->latest()->orderByDesc('id');
    }

    public function url(): string
    {
        return Storage::disk('public')->url($this->archivo);
    }

    /**
     * Crédito que va debajo de la foto: lo exigen las licencias libres y es justo con quien la hizo.
     *
     * @return array{texto: string, autor_url: ?string, licencia: ?string, licencia_url: ?string}
     */
    public function credito(): array
    {
        $autor = $this->autor ?: 'TravelPet';

        return match ($this->origen) {
            'wikimedia' => ['texto' => 'Foto: '.$autor, 'autor_url' => $this->fuente_url, 'licencia' => trim(($this->licencia ?: 'Licencia libre').' · Wikimedia Commons'), 'licencia_url' => $this->licencia_url],
            'cedida' => ['texto' => 'Foto cedida por '.$autor, 'autor_url' => null, 'licencia' => null, 'licencia_url' => null],
            default => ['texto' => 'Foto: '.$autor, 'autor_url' => null, 'licencia' => null, 'licencia_url' => null],
        };
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
