<?php

namespace App\Support;

use App\Models\Provincia;
use App\Models\Sitio;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Páginas para que Google encuentre la guía: una por provincia, por categoría, por pueblo
 * y por pueblo y categoría («Playas para mascotas en Conil de la Frontera»).
 */
class PaginasGuia
{
    /**
     * Con menos sitios la página es demasiado pobre: se puede abrir, pero Google no la indexa
     * ni va en el sitemap.
     */
    public const MINIMO = 3;

    /**
     * tipo => [trozo de la dirección, título de la página, nombre de cada sitio en plural]
     *
     * @var array<string, array{0: string, 1: string, 2: string}>
     */
    public const CATEGORIAS = [
        'Alojamiento' => ['alojamientos', 'Alojamientos que admiten mascotas', 'alojamientos'],
        'Restauración' => ['restaurantes-y-bares', 'Restaurantes y bares que admiten mascotas', 'restaurantes y bares'],
        'Playa' => ['playas', 'Playas para mascotas', 'playas'],
        'Espacio' => ['parques-y-paseos', 'Parques y paseos para mascotas', 'parques y paseos'],
        'Salud' => ['veterinarios', 'Veterinarios', 'veterinarios'],
        'Servicios' => ['servicios-para-mascotas', 'Servicios para mascotas', 'servicios'],
        'Ocio' => ['planes', 'Planes con mascota', 'planes'],
        'Transporte' => ['transporte', 'Transporte que admite mascotas', 'sitios de transporte'],
        'Comercio' => ['comercios', 'Comercios que admiten mascotas', 'comercios'],
    ];

    /**
     * Direcciones antiguas que ya no existen y a cuál llevan ahora (por si Google o alguien las tiene guardadas).
     *
     * @var array<string, string>
     */
    public const SLUGS_ANTIGUOS = [
        'transporte-y-ocio' => 'planes',
    ];

    public static function slug(string $tipo): string
    {
        return static::CATEGORIAS[$tipo][0];
    }

    public static function titulo(string $tipo): string
    {
        return static::CATEGORIAS[$tipo][1];
    }

    public static function tipoDelSlug(string $slug): ?string
    {
        foreach (static::CATEGORIAS as $tipo => [$s]) {
            if ($s === $slug) {
                return $tipo;
            }
        }

        return null;
    }

    /**
     * Sitios publicados de una provincia, en el orden de la guía: primero lo que admite mascotas
     * (y, dentro de cada respuesta, lo confirmado); al final lo que no las admite.
     */
    public static function sitios(Provincia $provincia): Builder
    {
        return Sitio::publicados()
            ->whereHas('municipio', fn (Builder $m) => $m->where('provincia_id', $provincia->id))
            ->with('municipio:id,nombre,slug')
            ->orderByRaw("CASE admite_perros WHEN 'si' THEN 0 WHEN 'condiciones' THEN 1 WHEN 'no_consta' THEN 2 ELSE 3 END")
            ->orderByDesc('verificado')
            ->orderBy('nombre');
    }

    /**
     * Cuántos sitios publicados hay por pueblo y categoría.
     *
     * @return Collection<int, object{municipio_id: int, tipo: string, n: int}>
     */
    public static function conteos(Provincia $provincia): Collection
    {
        return Sitio::publicados()
            ->whereHas('municipio', fn (Builder $m) => $m->where('provincia_id', $provincia->id))
            ->selectRaw('municipio_id, tipo, count(*) as n')
            ->groupBy('municipio_id', 'tipo')
            ->toBase()
            ->get()
            ->filter(fn (object $c) => isset(static::CATEGORIAS[$c->tipo]))
            ->values();
    }

    /**
     * Direcciones de todas las páginas con sitios suficientes, para el sitemap.
     *
     * @return list<string>
     */
    public static function paraSitemap(): array
    {
        $urls = [];
        foreach (Provincia::activas()->with('municipios:id,provincia_id,slug')->get() as $provincia) {
            $conteos = static::conteos($provincia);
            if ($conteos->sum('n') === 0) {
                continue;
            }
            $urls[] = route('guia', $provincia);
            foreach ($conteos->groupBy('tipo') as $tipo => $filas) {
                if ($filas->sum('n') >= static::MINIMO) {
                    $urls[] = route('guia.seccion', [$provincia, static::slug($tipo)]);
                }
            }
            $municipios = $provincia->municipios->keyBy('id');
            foreach ($conteos->groupBy('municipio_id') as $id => $filas) {
                $pueblo = $municipios[$id]->slug;
                if ($filas->sum('n') >= static::MINIMO) {
                    $urls[] = route('guia.seccion', [$provincia, $pueblo]);
                }
                foreach ($filas->where('n', '>=', static::MINIMO) as $fila) {
                    $urls[] = route('guia.pueblo', [$provincia, $pueblo, static::slug($fila->tipo)]);
                }
            }
        }

        return $urls;
    }
}
