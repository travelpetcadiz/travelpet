@php
    use App\Models\Foto;
    use App\Models\Municipio;
    use App\Models\Sitio;

    // Rediseño: misma información que la portada actual, con más presencia y más confianza a la vista
    $fotos = Foto::paraPortada()->get();
    $conteoTipos = Sitio::publicados()->selectRaw('tipo, count(*) as n')->groupBy('tipo')->pluck('n', 'tipo');
    $total = $conteoTipos->sum();
    $confirmados = Sitio::publicados()->where('verificado', true)->count();
    $pueblos = Sitio::publicados()->distinct('municipio_id')->count('municipio_id');

    // Últimos sitios confirmados por el propio sitio: prueba de que la guía está viva
    $recientes = Sitio::publicados()->where('verificado', true)->with(['municipio', 'fotos' => fn ($f) => $f->publicadas()])
        ->orderByDesc('fecha_comprobacion')->take(6)->get();

    $destinos = collect([
        'Cádiz' => 'Cádiz', 'Conil' => 'Conil de la Frontera', 'Tarifa' => 'Tarifa', 'Chiclana' => 'Chiclana de la Frontera',
        'El Puerto' => 'El Puerto de Santa María', 'Vejer' => 'Vejer de la Frontera', 'Jerez' => 'Jerez de la Frontera', 'Sanlúcar' => 'Sanlúcar de Barrameda',
    ])->map(function (string $nombre, string $corto) use ($fotos) {
        $m = Municipio::where('nombre', $nombre)->first();

        return [
            'corto' => $corto,
            'municipio' => $m,
            'sitios' => $m ? Sitio::publicados()->where('municipio_id', $m->id)->count() : 0,
            'foto' => $fotos->first(fn ($f) => $f->lugar && str_contains(mb_strtolower($f->lugar), mb_strtolower($corto))),
        ];
    })->filter(fn ($d) => $d['municipio'] && $d['sitios'] > 0)->values();
    $principal = $fotos->first();
@endphp
@component('layouts.app', ['title' => 'Boceto: rediseño de la portada', 'noindex' => true])
    @include('mockup.partes.barra', ['actual' => 'rediseno-portada'])

    {{-- 1. Primera impresión: foto grande de lado a lado y la pregunta en blanco encima --}}
    <section class="relative isolate overflow-hidden bg-tinta">
        <img src="{{ $principal?->url() ?? asset('hero.jpg') }}" alt="{{ $principal?->descripcion ?? 'Perro con correa en un parque' }}" fetchpriority="high"
             class="absolute inset-0 -z-10 size-full object-cover object-[50%_25%]">
        <div class="absolute inset-0 -z-10 bg-gradient-to-t from-tinta via-tinta/55 to-tinta/10 md:bg-gradient-to-r md:from-tinta/90 md:via-tinta/50 md:to-transparent"></div>
        @if ($principal && $principal->origen !== 'propia')
            <span class="absolute right-2 top-2 rounded-md bg-tinta/60 px-2 py-0.5 text-xs text-white">{{ $principal->credito()['texto'] }}</span>
        @endif

        <div class="mx-auto flex min-h-[470px] max-w-6xl flex-col justify-end px-4 pb-24 pt-40 md:min-h-[560px] md:justify-center md:pb-28">
            <p class="mb-3 inline-flex w-fit items-center gap-1.5 rounded-full bg-white/15 px-3 py-1 text-sm font-bold text-white ring-1 ring-white/30 backdrop-blur">
                @svg('heroicon-s-map-pin', 'size-4 text-albero', ['aria-hidden' => 'true'])Provincia de Cádiz · guía gratuita
            </p>
            <h1 class="max-w-2xl text-[2.6rem] font-extrabold leading-[1.05] text-white sm:text-6xl md:text-7xl">¿Puedo ir con mi mascota?</h1>
            <p class="mt-4 max-w-xl text-lg leading-snug text-white/90 md:text-xl">Hoteles, bares, playas y planes que admiten mascotas, con sus condiciones explicadas antes de que llegues.</p>
        </div>
    </section>

    {{-- 2. El buscador, en una tarjeta que se monta sobre la foto: se ve sin bajar --}}
    <section class="relative z-10 mx-auto -mt-16 max-w-6xl px-4">
        <form action="{{ route('mockup', 'rediseno-lista') }}" method="GET" role="search"
              class="grid gap-3 rounded-3xl bg-white p-4 shadow-xl ring-1 ring-linea md:max-w-3xl md:grid-cols-[minmax(0,1fr)_auto_auto] md:p-5">
            <label for="q-rediseno" class="sr-only">¿A qué pueblo o sitio quieres ir?</label>
            <div class="relative">
                @svg('heroicon-o-magnifying-glass', 'pointer-events-none absolute left-4 top-1/2 size-6 -translate-y-1/2 text-pino', ['aria-hidden' => 'true'])
                <input id="q-rediseno" name="q" type="search" placeholder="¿A qué pueblo o sitio vas?" class="campo min-h-14 rounded-2xl border-2 border-linea bg-cal pl-12 text-lg focus:bg-white">
            </div>
            <button type="submit" class="btn btn-pri min-h-14 rounded-2xl px-7 text-lg shadow-sm">@svg('heroicon-o-magnifying-glass', 'size-5', ['aria-hidden' => 'true'])Buscar</button>
            <a href="{{ route('mockup', 'rediseno-lista') }}" class="btn min-h-14 rounded-2xl px-5 text-lg">@svg('heroicon-o-map-pin', 'size-5', ['aria-hidden' => 'true'])Cerca de mí</a>
        </form>

        {{-- Las cifras que dan confianza, justo debajo del buscador --}}
        <ul class="mt-4 grid grid-cols-3 gap-2 text-center md:max-w-3xl">
            <li class="rounded-2xl bg-white px-2 py-3 ring-1 ring-linea">
                <span class="block font-display text-2xl font-extrabold text-pino">{{ $total }}</span>
                <span class="text-sm leading-tight text-gris">sitios</span>
            </li>
            <li class="rounded-2xl bg-white px-2 py-3 ring-1 ring-linea">
                <span class="block font-display text-2xl font-extrabold text-ok">{{ $confirmados }}</span>
                <span class="text-sm leading-tight text-gris">confirmados por el sitio</span>
            </li>
            <li class="rounded-2xl bg-white px-2 py-3 ring-1 ring-linea">
                <span class="block font-display text-2xl font-extrabold text-tinta">{{ $pueblos }}</span>
                <span class="text-sm leading-tight text-gris">pueblos</span>
            </li>
        </ul>
    </section>

    {{-- 3. Qué buscas: accesos grandes con el color de cada categoría --}}
    <section class="mx-auto max-w-6xl px-4 pt-12">
        <h2 class="text-2xl font-extrabold md:text-3xl">¿Qué buscas?</h2>
        <div class="mt-4 grid grid-cols-2 gap-3 md:grid-cols-4">
            @foreach (Sitio::PRINCIPALES as $clave)
                @php([$etiqueta, $color, $icono] = Sitio::TIPOS[$clave])
                <a href="{{ route('mockup', 'rediseno-lista') }}?tipo={{ urlencode($clave) }}"
                   class="group relative flex min-h-32 flex-col justify-between overflow-hidden rounded-2xl p-4 no-underline ring-1 ring-black/5 transition hover:-translate-y-0.5 hover:shadow-md"
                   style="background-color: {{ $color }}1A">
                    @svg('heroicon-o-'.$icono, 'absolute -bottom-4 -right-4 size-24 opacity-20 transition group-hover:scale-110', ['style' => 'color: '.$color, 'aria-hidden' => 'true'])
                    <span class="grid size-11 place-items-center rounded-xl bg-white shadow-sm">
                        <x-icono-tipo :tipo="$clave" class="size-6" />
                    </span>
                    <span>
                        <span class="block font-display text-lg font-extrabold leading-tight text-tinta">{{ $etiqueta }}</span>
                        <span class="text-sm text-gris">{{ $conteoTipos[$clave] ?? 0 }} sitios</span>
                    </span>
                </a>
            @endforeach
        </div>
        <div class="-mx-4 mt-3 flex gap-2 overflow-x-auto px-4 pb-1 [scrollbar-width:none] md:mx-0 md:flex-wrap md:px-0">
            @foreach (array_diff(array_keys(Sitio::TIPOS), Sitio::PRINCIPALES) as $clave)
                <a href="{{ route('mockup', 'rediseno-lista') }}?tipo={{ urlencode($clave) }}" class="chip min-h-11 no-underline"><x-icono-tipo :tipo="$clave" />{{ Sitio::TIPOS[$clave][0] }}</a>
            @endforeach
            <a href="{{ route('urgencias') }}" class="chip min-h-11 border-mal/30 bg-mal-suave text-mal no-underline">@svg('heroicon-o-lifebuoy', 'size-5', ['aria-hidden' => 'true'])Urgencias veterinarias 24 h</a>
        </div>
    </section>

    {{-- 4. Recién confirmados: sitios reales que nos han contestado --}}
    @if ($recientes->isNotEmpty())
        <section class="mx-auto max-w-6xl px-4 pt-12">
            <div class="flex items-end justify-between gap-3">
                <div>
                    <h2 class="text-2xl font-extrabold md:text-3xl">Confirmados por el propio sitio</h2>
                    <p class="mt-1 text-gris">Nos lo han dicho ellos. Los últimos en contestar:</p>
                </div>
            </div>
            <ul class="-mx-4 mt-4 flex snap-x gap-3 overflow-x-auto px-4 pb-3 [scrollbar-width:none] md:mx-0 md:grid md:grid-cols-3 md:overflow-visible md:px-0">
                @foreach ($recientes as $s)
                    <li class="w-72 flex-none snap-start md:w-auto">@include('mockup.partes.rediseno-tarjeta', ['sitio' => $s])</li>
                @endforeach
            </ul>
        </section>
    @endif

    {{-- 5. Destinos --}}
    @if ($destinos->isNotEmpty())
        <section class="mx-auto max-w-6xl px-4 pt-10">
            <h2 class="text-2xl font-extrabold md:text-3xl">Elige un pueblo</h2>
            <p class="mt-1 text-gris">Todo lo que admite mascotas en cada sitio.</p>
            <ul class="-mx-4 mt-4 flex snap-x gap-3 overflow-x-auto px-4 pb-2 [scrollbar-width:none] md:mx-0 md:grid md:grid-cols-4 md:overflow-visible md:px-0">
                @foreach ($destinos as $d)
                    <li class="w-40 flex-none snap-start md:w-auto">
                        <a href="{{ route('mockup', 'rediseno-lista') }}?municipio={{ $d['municipio']->id }}" class="group relative block aspect-[4/5] overflow-hidden rounded-2xl bg-pino no-underline shadow-sm md:aspect-[4/3]">
                            @if ($d['foto'])
                                <img src="{{ $d['foto']->url() }}" alt="" loading="lazy" class="absolute inset-0 size-full object-cover transition group-hover:scale-105">
                            @else
                                <span class="absolute inset-0 bg-gradient-to-br from-pino to-pino-oscuro"></span>
                                @svg('heroicon-o-map-pin', 'absolute -right-4 -top-4 size-32 text-white/10', ['aria-hidden' => 'true'])
                            @endif
                            <span class="absolute inset-0 bg-gradient-to-t from-tinta/80 to-transparent"></span>
                            <span class="absolute inset-x-0 bottom-0 p-3 text-white">
                                <span class="block font-display text-xl font-extrabold leading-tight">{{ $d['corto'] }}</span>
                                <span class="text-sm text-white/90">{{ $d['sitios'] }} sitios</span>
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    {{-- 6. Cómo funciona, en tres pasos --}}
    <section class="mx-auto max-w-6xl px-4 pt-12">
        <h2 class="text-2xl font-extrabold md:text-3xl">Así de fácil</h2>
        <ol class="mt-4 grid gap-3 md:grid-cols-3">
            @foreach ([
                ['magnifying-glass', 'Busca tu destino', 'Un pueblo, una playa o un hotel. O pulsa «Cerca de mí».'],
                ['list-bullet', 'Mira las condiciones', 'Suplemento, tamaño, si puede entrar dentro, fechas de playa…'],
                ['bookmark', 'Guárdalo en Mi viaje', 'Ten a mano tus sitios para la escapada.'],
            ] as $n => [$icono, $titulo, $texto])
                <li class="flex gap-4 rounded-2xl bg-white p-4 ring-1 ring-linea">
                    <span class="grid size-11 flex-none place-items-center rounded-full bg-albero font-display text-lg font-extrabold text-tinta">{{ $n + 1 }}</span>
                    <span>
                        <span class="flex items-center gap-1.5 font-bold">@svg('heroicon-o-'.$icono, 'size-5 text-pino', ['aria-hidden' => 'true']){{ $titulo }}</span>
                        <span class="text-gris">{{ $texto }}</span>
                    </span>
                </li>
            @endforeach
        </ol>
        <a href="{{ route('como-funciona') }}" class="mt-3 inline-flex min-h-11 items-center gap-2 font-bold text-pino underline">@svg('heroicon-o-question-mark-circle', 'size-5', ['aria-hidden' => 'true'])Cómo comprobamos cada dato</a>
    </section>

    {{-- 7. Cierre: invitación a sugerir, con el color de la marca --}}
    <section class="mx-auto max-w-6xl px-4 py-12">
        <div class="relative overflow-hidden rounded-3xl bg-pino p-6 text-white md:flex md:items-center md:justify-between md:p-10">
            @svg('heroicon-o-light-bulb', 'absolute -right-6 -top-6 size-40 text-white/10', ['aria-hidden' => 'true'])
            <div class="relative">
                <h2 class="text-2xl font-extrabold md:text-3xl">¿Conoces un sitio que falta?</h2>
                <p class="mt-1 text-white/85">Cuéntanoslo y lo comprobamos antes de publicarlo.</p>
            </div>
            <a href="{{ route('sugerir') }}" class="btn btn-albero relative mt-4 w-full md:mt-0 md:w-auto">@svg('heroicon-o-light-bulb', 'size-5', ['aria-hidden' => 'true'])Sugerir un sitio</a>
        </div>
    </section>
@endcomponent
