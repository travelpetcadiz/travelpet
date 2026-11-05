<?php

namespace App\Http\Controllers;

use App\Models\Municipio;
use App\Models\Provincia;
use App\Models\Sitio;
use App\Support\PaginasGuia;
use Illuminate\Support\Collection;
use Illuminate\View\View;

/**
 * Páginas para Google: la provincia, una categoría, un pueblo y un pueblo con una categoría.
 */
class GuiaController extends Controller
{
    public function provincia(Provincia $provincia): View
    {
        abort_unless($provincia->activa, 404);
        $conteos = PaginasGuia::conteos($provincia);
        abort_if($conteos->isEmpty(), 404);

        $porPueblo = $conteos->groupBy('municipio_id')->map->sum('n');
        $pueblos = Municipio::whereIn('id', $porPueblo->keys())->orderBy('nombre')->get()
            ->each(fn (Municipio $m) => $m->setAttribute('total', $porPueblo[$m->id]));

        return view('paginas.guia-provincia', [
            'provincia' => $provincia,
            'total' => $conteos->sum('n'),
            'categorias' => $this->categorias($conteos, fn (string $tipo) => route('guia.seccion', [$provincia, PaginasGuia::slug($tipo)])),
            'comarcas' => $pueblos->groupBy(fn (Municipio $m) => $m->comarca ?: 'Otros pueblos')->sortKeys(),
        ]);
    }

    /**
     * El segundo trozo de la dirección es una categoría («playas») o un pueblo («conil-de-la-frontera»).
     */
    public function seccion(Provincia $provincia, string $seccion): View
    {
        abort_unless($provincia->activa, 404);
        $tipo = PaginasGuia::tipoDelSlug($seccion);

        return $tipo ? $this->categoria($provincia, $tipo) : $this->pueblo($provincia, $this->municipio($provincia, $seccion));
    }

    public function puebloCategoria(Provincia $provincia, string $pueblo, string $categoria): View
    {
        abort_unless($provincia->activa, 404);
        $municipio = $this->municipio($provincia, $pueblo);
        $tipo = PaginasGuia::tipoDelSlug($categoria) ?? abort(404);
        $sitios = PaginasGuia::sitios($provincia)->where('municipio_id', $municipio->id)->where('tipo', $tipo)->get();
        abort_if($sitios->isEmpty(), 404);

        $conteos = PaginasGuia::conteos($provincia)->where('municipio_id', $municipio->id);

        return $this->pagina(
            h1: PaginasGuia::titulo($tipo).' en '.$municipio->nombre,
            migas: [
                [$provincia->nombre, route('guia', $provincia)],
                [$municipio->nombre, route('guia.seccion', [$provincia, $municipio->slug])],
            ],
            sitios: $sitios,
            grupos: [['titulo' => null, 'enlace' => null, 'enlaceTexto' => null, 'sitios' => $sitios]],
            otras: $this->categorias($conteos, fn (string $t) => route('guia.pueblo', [$provincia, $municipio->slug, PaginasGuia::slug($t)]), $tipo),
            otrasTitulo: 'Más en '.$municipio->nombre,
            mapa: route('inicio', ['municipio' => $municipio->id, 'tipo' => $tipo, 'vista' => 'mapa']),
            nombre: PaginasGuia::CATEGORIAS[$tipo][2],
        );
    }

    private function categoria(Provincia $provincia, string $tipo): View
    {
        $sitios = PaginasGuia::sitios($provincia)->where('tipo', $tipo)->get();
        abort_if($sitios->isEmpty(), 404);

        // Agrupados por pueblo, cada uno con enlace a su página
        $grupos = $sitios->groupBy('municipio_id')
            ->sortBy(fn (Collection $g) => $g->first()->municipio->nombre)
            ->map(fn (Collection $g) => [
                'titulo' => $g->first()->municipio->nombre,
                'enlace' => route('guia.pueblo', [$provincia, $g->first()->municipio->slug, PaginasGuia::slug($tipo)]),
                'enlaceTexto' => 'Ver solo '.$g->first()->municipio->nombre,
                'sitios' => $g,
            ])->values()->all();

        return $this->pagina(
            h1: PaginasGuia::titulo($tipo).' en la provincia de '.$provincia->nombre,
            migas: [[$provincia->nombre, route('guia', $provincia)]],
            sitios: $sitios,
            grupos: $grupos,
            otras: $this->categorias(PaginasGuia::conteos($provincia), fn (string $t) => route('guia.seccion', [$provincia, PaginasGuia::slug($t)]), $tipo),
            otrasTitulo: 'Más en la provincia de '.$provincia->nombre,
            mapa: route('inicio', ['tipo' => $tipo, 'vista' => 'mapa']),
            nombre: PaginasGuia::CATEGORIAS[$tipo][2],
        );
    }

    private function pueblo(Provincia $provincia, Municipio $municipio): View
    {
        $sitios = PaginasGuia::sitios($provincia)->where('municipio_id', $municipio->id)->get();
        abort_if($sitios->isEmpty(), 404);

        // Agrupados por categoría, en el mismo orden que el menú de la web
        $grupos = collect(array_keys(Sitio::TIPOS))
            ->filter(fn (string $tipo) => isset(PaginasGuia::CATEGORIAS[$tipo]) && $sitios->contains('tipo', $tipo))
            ->map(fn (string $tipo) => [
                'titulo' => Sitio::TIPOS[$tipo][0],
                'enlace' => route('guia.pueblo', [$provincia, $municipio->slug, PaginasGuia::slug($tipo)]),
                'enlaceTexto' => 'Ver solo '.PaginasGuia::CATEGORIAS[$tipo][2],
                'sitios' => $sitios->where('tipo', $tipo)->values(),
            ])->values()->all();

        return $this->pagina(
            h1: 'Sitios que admiten mascotas en '.$municipio->nombre,
            migas: [[$provincia->nombre, route('guia', $provincia)]],
            sitios: $sitios,
            grupos: $grupos,
            otras: [],
            otrasTitulo: null,
            mapa: route('inicio', ['municipio' => $municipio->id, 'vista' => 'mapa']),
        );
    }

    private function municipio(Provincia $provincia, string $slug): Municipio
    {
        return Municipio::where('provincia_id', $provincia->id)->where('slug', $slug)->firstOrFail();
    }

    /**
     * Enlaces a las categorías que tienen sitios, con cuántos hay.
     *
     * @return list<array{tipo: string, texto: string, total: int, url: string}>
     */
    private function categorias(Collection $conteos, \Closure $url, ?string $sinEsta = null): array
    {
        $porTipo = $conteos->groupBy('tipo')->map->sum('n');

        return collect(array_keys(PaginasGuia::CATEGORIAS))
            ->filter(fn (string $tipo) => $tipo !== $sinEsta && ($porTipo[$tipo] ?? 0) > 0)
            ->map(fn (string $tipo) => [
                'tipo' => $tipo,
                'texto' => Sitio::TIPOS[$tipo][0],
                'total' => $porTipo[$tipo],
                'url' => $url($tipo),
            ])->values()->all();
    }

    private function pagina(string $h1, array $migas, Collection $sitios, array $grupos, array $otras, ?string $otrasTitulo, string $mapa, string $nombre = 'sitios'): View
    {
        $soloServicios = $sitios->every(fn (Sitio $s) => $s->esServicio());

        return view('paginas.guia', [
            'h1' => $h1,
            'migas' => $migas,
            'grupos' => $grupos,
            'otras' => $otras,
            'otrasTitulo' => $otrasTitulo,
            'mapa' => $mapa,
            'total' => $sitios->count(),
            'nombre' => $nombre,
            'confirmados' => $sitios->where('verificado', true)->count(),
            'admite' => $soloServicios ? null : $sitios->reject->esServicio()->countBy('admite_perros'),
            'conMapa' => $sitios->whereNotNull('latitud')->isNotEmpty(),
            'noindex' => $sitios->count() < PaginasGuia::MINIMO,
            'jsonLd' => [
                '@context' => 'https://schema.org',
                '@graph' => [
                    [
                        '@type' => 'BreadcrumbList',
                        'itemListElement' => collect([['Inicio', route('inicio')], ...$migas, [$h1, url()->current()]])
                            ->map(fn (array $m, int $i) => ['@type' => 'ListItem', 'position' => $i + 1, 'name' => $m[0], 'item' => $m[1]])->all(),
                    ],
                    [
                        '@type' => 'ItemList',
                        'name' => $h1,
                        'numberOfItems' => $sitios->count(),
                        'itemListElement' => $sitios->values()
                            ->map(fn (Sitio $s, int $i) => ['@type' => 'ListItem', 'position' => $i + 1, 'url' => route('sitio', $s), 'name' => $s->nombre])->all(),
                    ],
                ],
            ],
        ]);
    }
}
