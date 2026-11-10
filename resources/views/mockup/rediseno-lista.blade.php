@php
    use App\Models\Municipio;
    use App\Models\Sitio;

    // Rediseño de la lista de sitios, con datos reales y sin Livewire (solo es un boceto)
    $q = trim((string) request('q', ''));
    $tipo = array_key_exists((string) request('tipo'), Sitio::TIPOS) ? (string) request('tipo') : 'Alojamiento';
    $municipio = request('municipio') ? Municipio::find((int) request('municipio')) : null;

    $consulta = Sitio::publicados()->with(['municipio', 'fotos' => fn ($f) => $f->publicadas()])
        ->when($q !== '', fn ($c) => $c->where(fn ($w) => $w->where('nombre', 'like', "%{$q}%")
            ->orWhereHas('municipio', fn ($m) => $m->where('nombre', 'like', "%{$q}%"))))
        ->when($tipo !== '' && ! $municipio, fn ($c) => $c->where('tipo', $tipo))
        ->when($municipio, fn ($c) => $c->where('municipio_id', $municipio->id));
    $total = (clone $consulta)->count();
    $sitios = $consulta->orderByRaw("admite_perros = 'no'")->orderByDesc('verificado')->orderBy('nombre')->take(12)->get();
    $conteoTipos = Sitio::publicados()->selectRaw('tipo, count(*) as n')->groupBy('tipo')->pluck('n', 'tipo');
    $titulo = $municipio ? 'Con tu mascota en '.$municipio->nombre : Sitio::TIPOS[$tipo][0].' con tu mascota';
@endphp
@component('layouts.app', ['title' => 'Boceto: rediseño de la lista', 'noindex' => true])
    @include('mockup.partes.barra', ['actual' => 'rediseno-lista'])

    {{-- Cabecera de la búsqueda: título claro de lo que estás viendo y el buscador debajo --}}
    <section class="bg-pino text-white">
        <div class="mx-auto max-w-6xl px-4 pb-5 pt-6">
            <h1 class="text-3xl font-extrabold leading-tight md:text-4xl">{{ $titulo }}</h1>
            <p class="mt-1 text-white/85"><strong class="text-white">{{ $total }}</strong> sitios en la provincia de Cádiz</p>
            <form action="{{ route('mockup', 'rediseno-lista') }}" method="GET" role="search" class="mt-4 flex gap-2">
                <label for="q-lista" class="sr-only">Buscar</label>
                <div class="relative flex-1">
                    @svg('heroicon-o-magnifying-glass', 'pointer-events-none absolute left-3 top-1/2 size-5 -translate-y-1/2 text-gris', ['aria-hidden' => 'true'])
                    <input id="q-lista" name="q" value="{{ $q }}" type="search" placeholder="Pueblo o sitio" class="campo min-h-12 rounded-2xl border-0 pl-10 text-tinta shadow-sm">
                </div>
                <a href="#" class="btn min-h-12 rounded-2xl border-0 px-3">@svg('heroicon-o-map', 'size-5', ['aria-hidden' => 'true'])Mapa</a>
            </form>
        </div>
    </section>

    {{-- Categorías y filtros, fijos al bajar --}}
    <section class="sticky top-[57px] z-20 border-b border-linea bg-white/95 backdrop-blur">
        <div class="mx-auto max-w-6xl px-4 py-3">
            <div class="-mx-4 flex gap-2 overflow-x-auto px-4 [scrollbar-width:none] md:mx-0 md:flex-wrap md:px-0">
                <a href="#" class="chip min-h-11 no-underline">@svg('heroicon-o-adjustments-horizontal', 'size-5', ['aria-hidden' => 'true'])Filtros</a>
                @foreach (Sitio::TIPOS as $clave => [$etiqueta])
                    <a href="{{ route('mockup', 'rediseno-lista') }}?tipo={{ urlencode($clave) }}" class="chip min-h-11 no-underline" aria-pressed="{{ ! $municipio && $tipo === $clave ? 'true' : 'false' }}">
                        <x-icono-tipo :tipo="$clave" />{{ $etiqueta }} <span class="font-normal opacity-70">{{ $conteoTipos[$clave] ?? 0 }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-4 py-6">
        {{-- Filtros rápidos de lo que más se pregunta, a un toque --}}
        <div class="-mx-4 mb-5 flex gap-2 overflow-x-auto px-4 [scrollbar-width:none] md:mx-0 md:flex-wrap md:px-0">
            @foreach ([['check-badge', 'Solo confirmados'], ['banknotes', 'Sin suplemento'], ['scale', 'Perros grandes'], ['home', 'Puede entrar dentro']] as [$icono, $texto])
                <a href="#" class="inline-flex min-h-10 flex-none items-center gap-1.5 rounded-full border border-pino/30 bg-pino-suave px-3 text-sm font-bold text-pino no-underline">@svg('heroicon-o-'.$icono, 'size-4', ['aria-hidden' => 'true']){{ $texto }}</a>
            @endforeach
        </div>

        <ul class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($sitios as $s)
                <li>@include('mockup.partes.rediseno-tarjeta', ['sitio' => $s])</li>
            @endforeach
        </ul>
        <p class="mt-6 flex items-center gap-2 text-sm text-gris"><x-confirmado />nos lo ha dicho el propio sitio. Lo demás, confírmalo antes de ir.</p>
    </section>
@endcomponent
