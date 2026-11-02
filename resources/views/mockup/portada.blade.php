@php
    use App\Models\Foto;
    use App\Models\Municipio;
    use App\Models\Sitio;

    // Fotos de Natalia marcadas para la portada (si aún no hay, las últimas subidas)
    $fotos = Foto::where('para_portada', true)->orderBy('orden')->latest()->get();
    if ($fotos->isEmpty()) {
        $fotos = Foto::latest()->take(6)->get();
    }

    // Cuántos sitios hay de cada categoría (para los accesos)
    $conteoTipos = Sitio::publicados()->selectRaw('tipo, count(*) as n')->groupBy('tipo')->pluck('n', 'tipo');
    $total = $conteoTipos->sum();

    // Destinos más buscados: el nombre corto que dice la gente y el del municipio
    $destinos = collect([
        'Cádiz' => 'Cádiz',
        'Conil' => 'Conil de la Frontera',
        'Tarifa' => 'Tarifa',
        'Chiclana' => 'Chiclana de la Frontera',
        'El Puerto' => 'El Puerto de Santa María',
        'Vejer' => 'Vejer de la Frontera',
        'Jerez' => 'Jerez de la Frontera',
        'Sanlúcar' => 'Sanlúcar de Barrameda',
    ])->map(function (string $nombre, string $corto) use ($fotos) {
        $m = Municipio::where('nombre', $nombre)->first();

        return [
            'corto' => $corto,
            'municipio' => $m,
            'sitios' => $m ? Sitio::publicados()->where('municipio_id', $m->id)->count() : 0,
            // Si alguna foto de la portada es de ese pueblo, se usa en su tarjeta
            'foto' => $fotos->first(fn ($f) => $f->lugar && str_contains(mb_strtolower($f->lugar), mb_strtolower($corto))),
        ];
    })->filter(fn ($d) => $d['municipio'] && $d['sitios'] > 0)->values();

    $confirmados = Sitio::publicados()->where('verificado', true)->count();
@endphp
@component('layouts.app', ['title' => 'Boceto: portada', 'noindex' => true])
    @include('mockup.partes.barra', ['actual' => 'portada'])

    {{-- Portada: carrusel con las fotos de portada; pasan solas y en el móvil se deslizan con el dedo.
         Las fotos propias van sin crédito; las de otros (cedidas, Wikimedia) lo llevan, porque lo exige su licencia --}}
    <section class="relative bg-tinta" aria-roledescription="carrusel" aria-label="Fotos de mascotas por Cádiz" data-carrusel>
        <ul class="flex h-[38svh] min-h-64 snap-x snap-mandatory overflow-x-auto [scrollbar-width:none] md:h-[560px] [&::-webkit-scrollbar]:hidden" data-pista>
            @forelse ($fotos as $i => $f)
                <li class="relative h-full w-full flex-none snap-center" aria-roledescription="foto" aria-label="{{ $i + 1 }} de {{ $fotos->count() }}">
                    <img src="{{ $f->url() }}" alt="{{ $f->descripcion }}" @if ($i === 0) fetchpriority="high" @else loading="lazy" @endif
                         class="size-full object-cover object-[50%_30%]">
                    @if ($f->origen !== 'propia')
                        <span class="absolute right-2 top-2 rounded-md bg-tinta/60 px-2 py-0.5 text-xs text-white">{{ $f->credito()['texto'] }}</span>
                    @endif
                </li>
            @empty
                <li class="h-full w-full flex-none"><img src="{{ asset('hero.jpg') }}" alt="" class="size-full object-cover"></li>
            @endforelse
        </ul>
        {{-- Degradado para que el título se lea sobre cualquier foto --}}
        <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-tinta/80 via-tinta/20 to-transparent md:bg-gradient-to-r md:from-tinta/75 md:via-tinta/30"></div>

        <div class="pointer-events-none absolute inset-x-0 bottom-0 md:inset-y-0">
            <div class="mx-auto flex h-full max-w-6xl items-end px-4 pb-16 md:items-center md:pb-0">
                <div class="max-w-xl text-white">
                    <h1 class="text-4xl font-extrabold leading-tight drop-shadow sm:text-5xl md:text-6xl">¿Puedo ir con mi mascota?</h1>
                    <p class="mt-2 text-lg text-white/90 drop-shadow">Hoteles, bares, playas y parques de Cádiz: si admiten mascotas y con qué condiciones.</p>
                </div>
            </div>
        </div>

        @if ($fotos->count() > 1)
            {{-- Flechas (solo ordenador) y puntos para saber cuántas fotos hay --}}
            <button type="button" data-anterior class="absolute left-3 top-1/2 hidden size-11 -translate-y-1/2 place-items-center rounded-full bg-white/85 text-tinta shadow hover:bg-white md:grid">
                @svg('heroicon-o-chevron-left', 'size-6', ['aria-hidden' => 'true'])<span class="sr-only">Foto anterior</span>
            </button>
            <button type="button" data-siguiente class="absolute right-3 top-1/2 hidden size-11 -translate-y-1/2 place-items-center rounded-full bg-white/85 text-tinta shadow hover:bg-white md:grid">
                @svg('heroicon-o-chevron-right', 'size-6', ['aria-hidden' => 'true'])<span class="sr-only">Foto siguiente</span>
            </button>
            <div class="absolute inset-x-0 bottom-11 flex justify-center gap-1.5 md:bottom-24">
                @foreach ($fotos as $i => $f)
                    <button type="button" data-punto="{{ $i }}" class="h-2 w-2 rounded-full bg-white/50 transition-all aria-[current=true]:w-6 aria-[current=true]:bg-white">
                        <span class="sr-only">Ver foto {{ $i + 1 }}</span>
                    </button>
                @endforeach
            </div>
        @endif
    </section>

    {{-- El buscador se monta sobre el borde de la foto: en el móvil se ve sin bajar --}}
    <section class="relative z-10 mx-auto -mt-8 max-w-6xl px-4 md:-mt-20">
        <form action="{{ route('mockup', 'resultados') }}" method="GET" role="search"
              class="grid gap-3 rounded-3xl border border-linea bg-white p-4 shadow-lg md:max-w-3xl md:p-6">
            <label for="q-boceto" class="font-display text-xl font-extrabold">¿Dónde quieres ir con tu mascota?</label>
            <div class="grid gap-2 sm:grid-cols-[minmax(0,1fr)_auto]">
                <div class="relative">
                    @svg('heroicon-o-magnifying-glass', 'pointer-events-none absolute left-4 top-1/2 size-6 -translate-y-1/2 text-gris', ['aria-hidden' => 'true'])
                    <input id="q-boceto" name="q" type="search" placeholder="Pueblo, playa, hotel…" class="campo min-h-14 rounded-2xl pl-12 text-lg">
                </div>
                <button type="submit" class="btn btn-pri min-h-14 rounded-2xl px-6 text-lg">@svg('heroicon-o-magnifying-glass', 'size-5', ['aria-hidden' => 'true'])Buscar</button>
            </div>
            <a href="{{ route('mockup', 'resultados') }}?cerca=1" class="btn min-h-12 w-full rounded-2xl sm:w-auto sm:justify-self-start">
                @svg('heroicon-o-map-pin', 'size-5', ['aria-hidden' => 'true'])Usar mi ubicación
            </a>
            <p class="text-sm text-gris">Tu ubicación solo se usa para ordenar por cercanía. No se guarda.</p>
        </form>
    </section>

    {{-- Qué buscas: lo que más se busca, en grande --}}
    <section class="mx-auto max-w-6xl px-4 pt-8">
        <h2 class="mb-3 text-2xl font-extrabold">¿Qué buscas?</h2>
        <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
            @foreach (Sitio::PRINCIPALES as $clave)
                <a href="{{ route('mockup', 'resultados') }}?tipo={{ urlencode($clave) }}"
                   class="tarjeta flex min-h-20 items-center gap-3 p-4 font-bold no-underline transition hover:border-pino hover:shadow-sm">
                    <span class="grid size-12 flex-none place-items-center rounded-2xl" style="background-color: {{ Sitio::TIPOS[$clave][1] }}1A">
                        <x-icono-tipo :tipo="$clave" class="size-7" />
                    </span>
                    <span class="leading-tight">{{ Sitio::TIPOS[$clave][0] }}<span class="block text-sm font-normal text-gris">{{ $conteoTipos[$clave] ?? 0 }} sitios</span></span>
                </a>
            @endforeach
        </div>
        <p class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-2 text-sm">
            <span class="text-gris">También:</span>
            @foreach (array_diff(array_keys(Sitio::TIPOS), Sitio::PRINCIPALES) as $clave)
                <a href="{{ route('mockup', 'resultados') }}?tipo={{ urlencode($clave) }}" class="inline-flex min-h-9 items-center gap-1.5 font-bold text-tinta underline decoration-linea underline-offset-4 hover:decoration-tinta">
                    <x-icono-tipo :tipo="$clave" />{{ Sitio::TIPOS[$clave][0] }}
                </a>
            @endforeach
            <a href="{{ route('urgencias') }}" class="inline-flex min-h-9 items-center gap-1.5 font-bold text-mal underline decoration-mal/30 underline-offset-4 hover:decoration-mal">
                @svg('heroicon-o-lifebuoy', 'size-5', ['aria-hidden' => 'true'])Urgencias veterinarias 24 h
            </a>
        </p>
    </section>

    {{-- Destinos: los pueblos que más se buscan --}}
    @if ($destinos->isNotEmpty())
        <section class="mx-auto max-w-6xl px-4 pt-10">
            <h2 class="text-2xl font-extrabold">Destinos</h2>
            <p class="mb-3 text-gris">Elige un pueblo y mira todo lo que admite mascotas.</p>
            <ul class="-mx-4 flex snap-x gap-3 overflow-x-auto px-4 pb-2 [scrollbar-width:none] md:mx-0 md:grid md:grid-cols-4 md:overflow-visible md:px-0">
                @foreach ($destinos as $d)
                    <li class="w-40 flex-none snap-start md:w-auto">
                        <a href="{{ route('mockup', 'resultados') }}?municipio={{ $d['municipio']->id }}" class="group relative block aspect-[4/5] overflow-hidden rounded-2xl bg-pino no-underline md:aspect-[4/3]">
                            @if ($d['foto'])
                                <img src="{{ $d['foto']->url() }}" alt="" loading="lazy" class="absolute inset-0 size-full object-cover transition group-hover:scale-105">
                            @else
                                {{-- Sin foto del pueblo: color de la marca con el mar de fondo --}}
                                <span class="absolute inset-0 bg-gradient-to-br from-pino to-pino-oscuro"></span>
                                @svg('heroicon-o-map-pin', 'absolute -right-4 -top-4 size-32 text-white/10', ['aria-hidden' => 'true'])
                            @endif
                            <span class="absolute inset-0 bg-gradient-to-t from-tinta/75 to-transparent"></span>
                            <span class="absolute inset-x-0 bottom-0 p-3 text-white">
                                <span class="block font-display text-xl font-extrabold leading-tight">{{ $d['corto'] }}</span>
                                <span class="text-sm text-white/90">{{ $d['sitios'] }} sitios</span>
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>
            @if ($destinos->contains(fn ($d) => ! $d['foto']))
                <p class="mt-1 text-sm text-gris">Boceto: los pueblos sin foto llevan el color de la marca. Si subes una foto y en «Lugar» pones el pueblo, saldrá aquí.</p>
            @endif
        </section>
    @endif

    {{-- Por qué fiarse: lo que distingue a TravelPet de una lista cualquiera --}}
    <section class="mx-auto max-w-6xl px-4 pt-10">
        <h2 class="mb-3 text-2xl font-extrabold">¿Por qué fiarte de TravelPet?</h2>
        <ul class="grid gap-3 md:grid-cols-3">
            <li class="tarjeta flex gap-3 p-4">
                @svg('heroicon-o-check-badge', 'size-8 flex-none text-ok', ['aria-hidden' => 'true'])
                <div>
                    <h3 class="font-bold">Confirmado por el propio sitio</h3>
                    <p class="text-gris">Si un hotel o un bar nos lo confirma, lo marcamos. Ya hay {{ $confirmados }} sitios así.</p>
                    <x-confirmado class="mt-1.5" />
                </div>
            </li>
            <li class="tarjeta flex gap-3 p-4">
                @svg('heroicon-o-shield-check', 'size-8 flex-none text-pino', ['aria-hidden' => 'true'])
                <div>
                    <h3 class="font-bold">Sabes de dónde sale cada dato</h3>
                    <p class="text-gris">Cada ficha dice de dónde sale y cuándo se comprobó. Lo que no sabemos, lo decimos.</p>
                </div>
            </li>
            <li class="tarjeta flex gap-3 p-4">
                @svg('heroicon-o-heart', 'size-8 flex-none text-albero', ['aria-hidden' => 'true'])
                <div>
                    <h3 class="font-bold">Gratis y hecha en Cádiz</h3>
                    <p class="text-gris">{{ $total }} sitios con sus condiciones, para que no te lleves sorpresas al llegar.</p>
                </div>
            </li>
        </ul>
        <a href="{{ route('como-funciona') }}" class="mt-3 inline-flex min-h-11 items-center gap-2 font-bold text-pino underline">@svg('heroicon-o-question-mark-circle', 'size-5', ['aria-hidden' => 'true'])Así funciona TravelPet</a>
    </section>

    {{-- Invitación a participar --}}
    <section class="mx-auto max-w-6xl px-4 py-10">
        <div class="flex flex-col gap-4 rounded-3xl bg-pino-suave p-6 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-extrabold">¿Conoces un sitio que falta?</h2>
                <p class="text-gris">Cuéntanoslo y lo comprobamos antes de publicarlo.</p>
            </div>
            <a href="{{ route('sugerir') }}" class="btn">@svg('heroicon-o-light-bulb', 'size-5', ['aria-hidden' => 'true'])Sugerir un sitio</a>
        </div>
    </section>
    <script>
        // Carrusel de la portada: pasa de foto cada 5 segundos y se para si la persona lo toca o tiene el movimiento reducido
        (() => {
            const c = document.querySelector('[data-carrusel]');
            const pista = c?.querySelector('[data-pista]');
            if (! pista || pista.children.length < 2) return;
            const total = pista.children.length;
            const puntos = [...c.querySelectorAll('[data-punto]')];
            const actual = () => Math.round(pista.scrollLeft / pista.clientWidth);
            const ir = i => pista.scrollTo({ left: ((i + total) % total) * pista.clientWidth, behavior: 'smooth' });
            const marcar = () => puntos.forEach((p, i) => p.setAttribute('aria-current', i === actual() ? 'true' : 'false'));
            let parado = matchMedia('(prefers-reduced-motion: reduce)').matches;
            setInterval(() => { if (! parado && ! document.hidden) ir(actual() + 1); }, 5000);
            pista.addEventListener('scroll', marcar, { passive: true });
            ['pointerdown', 'touchstart', 'focusin'].forEach(e => c.addEventListener(e, () => (parado = true), { passive: true }));
            c.addEventListener('mouseenter', () => (parado = true));
            c.addEventListener('mouseleave', () => (parado = matchMedia('(prefers-reduced-motion: reduce)').matches));
            c.querySelector('[data-anterior]')?.addEventListener('click', () => ir(actual() - 1));
            c.querySelector('[data-siguiente]')?.addEventListener('click', () => ir(actual() + 1));
            puntos.forEach((p, i) => p.addEventListener('click', () => ir(i)));
            marcar();
        })();
    </script>
@endcomponent
