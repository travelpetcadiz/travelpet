@php
    use App\Models\Municipio;
    use App\Models\Sitio;

    // Boceto con datos reales: lee lo mismo que la búsqueda de verdad, pero sin Livewire
    $q = trim((string) request('q', ''));
    $tipo = array_key_exists((string) request('tipo'), Sitio::TIPOS) ? (string) request('tipo') : '';
    $municipio = request('municipio') ? Municipio::find((int) request('municipio')) : null;
    $cerca = request()->boolean('cerca');
    $vista = request('vista') === 'mapa' ? 'mapa' : 'lista';

    // Sin búsqueda, el boceto enseña Conil para que haya de todo
    if ($q === '' && $tipo === '' && ! $municipio && ! $cerca) {
        $municipio = Municipio::where('nombre', 'Conil de la Frontera')->first();
    }

    $consulta = Sitio::publicados()->with(['municipio', 'fotos' => fn ($f) => $f->publicadas()])
        ->when($q !== '', fn ($c) => $c->where(fn ($w) => $w->where('nombre', 'like', "%{$q}%")
            ->orWhereHas('municipio', fn ($m) => $m->where('nombre', 'like', "%{$q}%"))))
        ->when($tipo !== '', fn ($c) => $c->where('tipo', $tipo))
        ->when($municipio, fn ($c) => $c->where('municipio_id', $municipio->id));
    $total = (clone $consulta)->count();

    // «Usar mi ubicación»: en el boceto se simula que estás en el centro de Chiclana
    $yo = ['lat' => 36.4192, 'lng' => -6.1490];
    $sitios = $consulta->get()
        ->map(function (Sitio $s) use ($yo) {
            $s->distancia_km = $s->latitud
                ? 6371 * 2 * asin(sqrt(sin(deg2rad($s->latitud - $yo['lat']) / 2) ** 2 + cos(deg2rad($yo['lat'])) * cos(deg2rad($s->latitud)) * sin(deg2rad($s->longitud - $yo['lng']) / 2) ** 2))
                : null;

            return $s;
        })
        ->when($cerca, fn ($c) => $c->filter(fn ($s) => $s->distancia_km !== null)->sortBy('distancia_km'),
            fn ($c) => $c->sortBy([fn ($a, $b) => (int) ($a->admite_perros === 'no') <=> (int) ($b->admite_perros === 'no'), ['verificado', 'desc'], ['nombre', 'asc']]))
        ->values();
    // Paginación de 12 en 12, como en la web pero con flechas y números
    $porPagina = 12;
    $paginador = new \Illuminate\Pagination\LengthAwarePaginator($sitios->forPage(\Illuminate\Pagination\Paginator::resolveCurrentPage(), $porPagina)->values(), $sitios->count(), $porPagina, null, ['path' => route('mockup', 'resultados'), 'query' => request()->except('page')]);
    $sitios = $paginador->getCollection();

    $distancia = fn ($km) => $km < 1 ? 'a '.(round($km * 10) * 100).' m' : 'a '.number_format($km, $km < 10 ? 1 : 0, ',', '.').' km';
    // Lo práctico que sabemos de cada sitio, con su icono (null = no lo sabemos y no sale)
    $hechos = fn (Sitio $s) => array_values(array_filter([
        $s->admite_grandes ? ['scale', 'Perros grandes'] : null,
        $s->sin_suplemento ? ['banknotes', 'Sin suplemento'] : null,
        $s->puede_entrar ? ['home', 'Dentro del local'] : null,
        $s->sin_correa ? ['bolt', 'Sin correa'] : null,
    ]));
    // Borde de color según la respuesta: se entiende de un vistazo al bajar por la lista
    $borde = fn (Sitio $s) => $s->esServicio() && $s->admite_perros !== 'no' ? 'border-l-linea' : match ($s->admite_perros) {
        'si' => 'border-l-ok', 'condiciones' => 'border-l-albero', 'no' => 'border-l-mal', default => 'border-l-linea',
    };

    // Puntos del mapa del boceto: se colocan dentro de un rectángulo según su latitud y longitud
    $conPunto = $sitios->filter(fn ($s) => $s->latitud);
    $caja = $conPunto->isEmpty() ? null : [
        'minLat' => $conPunto->min('latitud') - 0.01, 'maxLat' => $conPunto->max('latitud') + 0.01,
        'minLng' => $conPunto->min('longitud') - 0.015, 'maxLng' => $conPunto->max('longitud') + 0.015,
    ];
    $posicion = fn (Sitio $s) => [
        'x' => round(($s->longitud - $caja['minLng']) / max(0.0001, $caja['maxLng'] - $caja['minLng']) * 100, 2),
        'y' => round(($caja['maxLat'] - $s->latitud) / max(0.0001, $caja['maxLat'] - $caja['minLat']) * 100, 2),
    ];

    $titulo = match (true) {
        $cerca => 'Cerca de ti',
        (bool) $municipio => ($tipo ? Sitio::TIPOS[$tipo][0].' en ' : 'Con mascota en ').$municipio->nombre,
        $tipo !== '' => Sitio::TIPOS[$tipo][0],
        default => '«'.$q.'»',
    };
    $enlace = fn (array $cambios) => route('mockup', 'resultados').'?'.http_build_query(array_filter(array_merge([
        'q' => $q, 'tipo' => $tipo, 'municipio' => $municipio?->id, 'cerca' => $cerca ? 1 : null, 'vista' => $vista === 'mapa' ? 'mapa' : null,
    ], $cambios), fn ($v) => $v !== null && $v !== ''));
@endphp
@component('layouts.app', ['title' => 'Boceto: resultados', 'noindex' => true])
    @include('mockup.partes.barra', ['actual' => 'resultados'])

    {{-- Barra de búsqueda: corta, para que en el móvil se vean los resultados enseguida --}}
    <section class="border-b border-linea bg-white">
        <div class="mx-auto grid max-w-6xl gap-3 px-4 py-3">
            <form action="{{ route('mockup', 'resultados') }}" method="GET" role="search" class="flex gap-2">
                <label for="q-res" class="sr-only">Buscar</label>
                <div class="relative flex-1">
                    @svg('heroicon-o-magnifying-glass', 'pointer-events-none absolute left-3 top-1/2 size-5 -translate-y-1/2 text-gris', ['aria-hidden' => 'true'])
                    <input id="q-res" name="q" type="search" value="{{ $q ?: $municipio?->nombre }}" placeholder="Pueblo, playa, hotel…" class="campo pl-10">
                </div>
                <button type="submit" class="btn btn-pri">@svg('heroicon-o-magnifying-glass', 'size-5', ['aria-hidden' => 'true'])<span class="sr-only sm:not-sr-only">Buscar</span></button>
            </form>

            <div class="-mx-4 flex gap-2 overflow-x-auto px-4 pb-1 [scrollbar-width:none] md:mx-0 md:flex-wrap md:px-0">
                <a href="{{ $enlace(['tipo' => null]) }}" class="chip no-underline" aria-pressed="{{ $tipo === '' ? 'true' : 'false' }}">Todo</a>
                @foreach (Sitio::TIPOS as $clave => [$etiqueta])
                    <a href="{{ $enlace(['tipo' => $clave]) }}" class="chip no-underline" aria-pressed="{{ $tipo === $clave ? 'true' : 'false' }}"><x-icono-tipo :tipo="$clave" />{{ $etiqueta }}</a>
                @endforeach
            </div>

            <div class="flex flex-wrap items-center gap-2 text-sm">
                <button type="button" class="inline-flex min-h-10 items-center gap-2 rounded-full border border-linea bg-white px-3 font-bold">
                    @svg('heroicon-o-adjustments-horizontal', 'size-5', ['aria-hidden' => 'true'])Filtros
                </button>
                @if ($cerca)
                    <span class="sello bg-pino-suave px-3 py-2 text-sm text-pino">@svg('heroicon-s-map-pin', 'size-4', ['aria-hidden' => 'true'])Ordenado por cercanía</span>
                @else
                    <a href="{{ $enlace(['cerca' => 1]) }}" class="inline-flex min-h-10 items-center gap-2 rounded-full border border-linea bg-white px-3 font-bold no-underline">@svg('heroicon-o-map-pin', 'size-5', ['aria-hidden' => 'true'])Cerca de mí</a>
                @endif
                {{-- Lista o mapa: en el ordenador se ven los dos a la vez --}}
                <span class="ml-auto inline-flex rounded-full border border-linea bg-cal p-1 lg:hidden" role="group" aria-label="Cómo ver los resultados">
                    <a href="{{ $enlace(['vista' => null]) }}" @class(['inline-flex min-h-9 items-center gap-1.5 rounded-full px-3 font-bold no-underline', 'bg-white shadow-sm' => $vista === 'lista']) aria-current="{{ $vista === 'lista' ? 'true' : 'false' }}">@svg('heroicon-o-list-bullet', 'size-5', ['aria-hidden' => 'true'])Lista</a>
                    <a href="{{ $enlace(['vista' => 'mapa']) }}" @class(['inline-flex min-h-9 items-center gap-1.5 rounded-full px-3 font-bold no-underline', 'bg-white shadow-sm' => $vista === 'mapa']) aria-current="{{ $vista === 'mapa' ? 'true' : 'false' }}">@svg('heroicon-o-map', 'size-5', ['aria-hidden' => 'true'])Mapa</a>
                </span>
            </div>
        </div>
    </section>

    <div class="mx-auto max-w-6xl px-4 py-5 lg:grid lg:grid-cols-[minmax(0,1fr)_minmax(0,0.85fr)] lg:gap-6">
        {{-- Lista --}}
        <section @class(['hidden lg:block' => $vista === 'mapa'])>
            <h1 class="text-2xl font-extrabold">{{ $titulo }}</h1>
            <p class="mb-3 text-sm text-gris">{{ $total }} {{ $total === 1 ? 'sitio' : 'sitios' }}@if ($cerca) · Boceto: simulamos que estás en el centro de Chiclana @endif</p>

            @if ($sitios->isEmpty())
                <div class="tarjeta p-8 text-center">
                    <p class="text-lg font-bold">No hay sitios con esta búsqueda.</p>
                    <p class="mt-1 text-gris">Prueba con otro pueblo o quita la categoría.</p>
                    <a href="{{ route('mockup', 'resultados') }}" class="btn mt-4">@svg('heroicon-o-x-mark', 'size-5', ['aria-hidden' => 'true'])Quitar filtros</a>
                </div>
            @else
                <p class="mb-3 flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-gris">
                    <span class="inline-flex items-center gap-1.5"><span class="h-4 w-1.5 rounded-full bg-ok"></span>Admite</span>
                    <span class="inline-flex items-center gap-1.5"><span class="h-4 w-1.5 rounded-full bg-albero"></span>Con condiciones</span>
                    <span class="inline-flex items-center gap-1.5"><span class="h-4 w-1.5 rounded-full bg-mal"></span>No admite</span>
                </p>
                <ul class="grid gap-3">
                    @foreach ($sitios as $s)
                        @php
                            $foto = $s->fotos->first();
                            $practico = $hechos($s);
                            $estadoHoy = $s->hoy();
                        @endphp
                        <li>
                            <a href="{{ route('mockup', 'ficha') }}?sitio={{ $s->slug }}"
                               class="tarjeta flex gap-3 border-l-4 p-3 no-underline transition hover:border-gris hover:shadow-sm {{ $borde($s) }}">
                                {{-- Foto del sitio o, si no hay, el icono de su categoría --}}
                                @if ($foto)
                                    <img src="{{ $foto->url() }}" alt="" loading="lazy" class="size-20 flex-none rounded-xl object-cover sm:size-24">
                                @else
                                    <span class="hidden size-24 flex-none place-items-center rounded-xl sm:grid" style="background-color: {{ $s->color() }}14">
                                        <x-icono-tipo :tipo="$s->tipo" class="size-9" />
                                    </span>
                                @endif
                                <span class="grid min-w-0 flex-1 content-start gap-1.5">
                                    <span class="flex flex-wrap gap-1.5">
                                        @if (! $s->esServicio() || $s->admite_perros === 'no')
                                            <x-admite :valor="$s->admite_perros" class="px-2 py-1 text-sm" />
                                        @endif
                                        @if ($s->verificado)
                                            <x-confirmado class="px-2 py-1 text-sm" />
                                        @endif
                                        @if ($s->urgencias_24h)
                                            <span class="sello bg-mal-suave px-2 py-1 text-sm text-mal">@svg('heroicon-s-lifebuoy', 'size-4', ['aria-hidden' => 'true'])Urgencias 24 h</span>
                                        @endif
                                        @if ($estadoHoy)
                                            <span class="sello px-2 py-1 text-sm {{ $estadoHoy['puede'] ? 'bg-ok-suave text-ok' : 'bg-mal-suave text-mal' }}">@svg('heroicon-s-calendar-days', 'size-4', ['aria-hidden' => 'true']){{ $estadoHoy['puede'] ? 'Hoy sí' : 'Hoy no' }}</span>
                                        @endif
                                    </span>
                                    <span class="font-display text-lg font-bold leading-snug text-tinta">{{ $s->nombre }}</span>
                                    <span class="flex flex-wrap items-center gap-x-2 text-sm text-gris">
                                        <span class="inline-flex items-center gap-1.5"><x-icono-tipo :tipo="$s->tipo" />{{ $s->subtipo ?: $s->etiquetaTipo() }}</span>
                                        <span>· {{ $s->municipio->nombre }}</span>
                                        @if ($cerca && $s->distancia_km !== null)
                                            <span class="font-bold text-pino">· {{ $distancia($s->distancia_km) }}</span>
                                        @endif
                                    </span>
                                    @if ($practico)
                                        <span class="flex flex-wrap gap-x-3 gap-y-1 text-sm text-tinta">
                                            @foreach (array_slice($practico, 0, 3) as [$icono, $texto])
                                                <span class="inline-flex items-center gap-1">@svg('heroicon-o-'.$icono, 'size-4 text-gris', ['aria-hidden' => 'true']){{ $texto }}</span>
                                            @endforeach
                                        </span>
                                    @elseif ($s->esServicio() && $s->temporada)
                                        <span class="flex items-start gap-1.5 text-sm text-gris">@svg('heroicon-o-clock', 'mt-0.5 size-4 flex-none', ['aria-hidden' => 'true'])<span class="line-clamp-1">{{ $s->temporada }}</span></span>
                                    @elseif ($s->zonas_permitidas || $s->otras_condiciones)
                                        <span class="line-clamp-1 text-sm text-gris">{{ $s->zonas_permitidas ?: $s->otras_condiciones }}</span>
                                    @endif
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>
                {{ $paginador->links('mockup.partes.paginacion') }}
            @endif
        </section>

        {{-- Mapa: en el ordenador, siempre al lado de la lista; en el móvil, al pulsar «Mapa» --}}
        <section @class(['lg:block', 'hidden' => $vista === 'lista']) aria-label="Mapa">
            <div class="sticky top-[72px] grid gap-2">
                <div class="relative h-[calc(100svh-260px)] min-h-[380px] overflow-hidden rounded-2xl border border-linea bg-mar lg:h-[calc(100svh-120px)]">
                    {{-- Fondo de boceto: en la web real aquí va el mapa de siempre --}}
                    <div class="absolute inset-0 opacity-60" style="background-image: linear-gradient(var(--color-linea) 1px, transparent 1px), linear-gradient(90deg, var(--color-linea) 1px, transparent 1px); background-size: 48px 48px;"></div>
                    @if ($caja)
                        @foreach ($conPunto as $s)
                            @php($p = $posicion($s))
                            <a href="{{ route('mockup', 'ficha') }}?sitio={{ $s->slug }}" title="{{ $s->nombre }}"
                               class="absolute grid size-9 -translate-x-1/2 -translate-y-full place-items-center rounded-full rounded-br-none border-2 bg-white shadow-md rotate-45 {{ $s->verificado ? 'border-ok' : 'border-white' }}"
                               style="left: calc(8% + {{ $p['x'] }} * 0.84%); top: calc(10% + {{ $p['y'] }} * 0.8%);">
                                <span class="-rotate-45"><x-icono-tipo :tipo="$s->tipo" class="size-5" /></span>
                                <span class="sr-only">{{ $s->nombre }}</span>
                            </a>
                        @endforeach
                    @endif
                    <p class="absolute inset-x-3 top-3 rounded-xl bg-white/90 px-3 py-2 text-sm text-gris shadow-sm">Boceto: en la web real aquí va el mapa de siempre, con los iconos de cada categoría.</p>
                    <span class="absolute bottom-3 right-3 btn min-h-10 py-1.5 shadow">@svg('heroicon-o-map-pin', 'size-5', ['aria-hidden' => 'true'])Mi ubicación</span>
                </div>

                {{-- En el móvil, con el mapa abierto, las tarjetas van debajo y se deslizan de lado --}}
                @if ($vista === 'mapa')
                    <ul class="-mx-4 flex snap-x gap-3 overflow-x-auto px-4 pb-2 lg:hidden">
                        @foreach ($sitios as $s)
                            <li class="w-72 flex-none snap-start">
                                <a href="{{ route('mockup', 'ficha') }}?sitio={{ $s->slug }}" class="tarjeta grid gap-1.5 border-l-4 p-3 no-underline {{ $borde($s) }}">
                                    <span class="flex flex-wrap gap-1.5">
                                        @if (! $s->esServicio() || $s->admite_perros === 'no')<x-admite :valor="$s->admite_perros" />@endif
                                        @if ($s->verificado)<x-confirmado />@endif
                                    </span>
                                    <span class="truncate font-display font-bold text-tinta">{{ $s->nombre }}</span>
                                    <span class="flex items-center gap-1.5 text-sm text-gris"><x-icono-tipo :tipo="$s->tipo" />{{ $s->subtipo ?: $s->etiquetaTipo() }} · {{ $s->municipio->nombre }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </section>
    </div>

    {{-- En el móvil, con la lista abierta, el botón del mapa queda siempre a mano --}}
    @if ($vista === 'lista')
        <a href="{{ $enlace(['vista' => 'mapa']) }}" class="btn fixed bottom-5 left-1/2 z-30 -translate-x-1/2 rounded-full border-tinta bg-tinta px-5 text-white shadow-lg lg:hidden">
            @svg('heroicon-o-map', 'size-5', ['aria-hidden' => 'true'])Ver en el mapa
        </a>
    @endif
@endcomponent
