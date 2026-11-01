<?php

namespace App\Support;

use App\Models\Municipio;
use App\Models\Provincia;
use App\Models\Sitio;
use Illuminate\Support\Facades\Log;

/**
 * Cambios de datos desde migraciones (lo que se sube a GitHub y se aplica al desplegar).
 *
 * Pensado para que sea difícil romper nada:
 * - Solo toca los campos que se le pasan; el resto, incluido lo editado en el panel, se queda igual.
 * - Si un sitio no existe, lo anota en el log y sigue: el despliegue no se cae.
 * - Cada campo cambiado queda en el historial con la fuente indicada.
 *
 * Campos válidos: los de la tabla sitios (nombre, tipo, subtipo, direccion, latitud, longitud, contacto,
 * admite_perros, tamano_maximo, suplemento, zonas_permitidas, temporada, otras_condiciones, fuente,
 * fecha_comprobacion, verificado, fuente_oficial, publicado).
 */
class DatosGuia
{
    public const ORIGEN = 'GitHub';

    /** Actualiza un sitio existente. Devuelve false si no lo encuentra. */
    public static function actualizar(string $nombre, string $municipio, array $campos, string $provincia = 'cadiz'): bool
    {
        $sitio = static::buscar($nombre, $municipio, $provincia);
        if (! $sitio) {
            Log::warning("DatosGuia: no existe «{$nombre}» en {$municipio}; no se actualiza.");

            return false;
        }

        static::conOrigen(fn () => $sitio->update($campos));

        return true;
    }

    /** Crea un sitio, o lo actualiza si ya existe con ese nombre en ese municipio. */
    public static function crearOActualizar(string $nombre, string $municipio, array $campos, string $provincia = 'cadiz', ?string $comarca = null): Sitio
    {
        if ($sitio = static::buscar($nombre, $municipio, $provincia)) {
            static::conOrigen(fn () => $sitio->update($campos));

            return $sitio;
        }

        $prov = Provincia::where('slug', $provincia)->orWhere('nombre', $provincia)->firstOrFail();
        $muni = Municipio::firstOrCreate(
            ['provincia_id' => $prov->id, 'nombre' => $municipio],
            ['comarca' => $comarca],
        );

        return Sitio::create(['tipo' => 'Servicios', 'fecha_comprobacion' => now(), ...$campos, 'nombre' => $nombre, 'municipio_id' => $muni->id]);
    }

    /** Deja de mostrar un sitio en la web sin borrarlo (se puede volver a publicar desde el panel). */
    public static function ocultar(string $nombre, string $municipio, string $provincia = 'cadiz'): bool
    {
        return static::actualizar($nombre, $municipio, ['publicado' => false], $provincia);
    }

    public static function buscar(string $nombre, string $municipio, string $provincia = 'cadiz'): ?Sitio
    {
        return Sitio::query()
            ->where('nombre', $nombre)
            ->whereHas('municipio', fn ($q) => $q->where('nombre', $municipio)
                ->whereHas('provincia', fn ($p) => $p->where('slug', $provincia)->orWhere('nombre', $provincia)))
            ->first();
    }

    private static function conOrigen(callable $fn): void
    {
        $antes = Sitio::$origenCambios;
        Sitio::$origenCambios = static::ORIGEN;
        try {
            $fn();
        } finally {
            Sitio::$origenCambios = $antes;
        }
    }
}
