@php
    use App\Models\Sitio;
    $practicos = ['grandes' => 'Admiten perros grandes (más de 25 kg)', 'gratis' => 'Sin suplemento por la mascota', 'dentro' => 'Puede estar dentro del local, no solo en la terraza', 'suelto' => 'Tiene zona para ir sin correa', 'hoy' => 'Playas a las que se puede ir hoy'];
    $nPracticos = collect(array_keys($practicos))->filter(fn ($k) => $this->{$k})->count();
    $hayFiltros = $q !== '' || $tipo !== '' || $provincia !== '' || $comarca !== '' || $municipio !== '' || $admite !== '' || $verificados || $nPracticos || $urgencias;
    $distancia = fn ($km) => ($km = (float) $km) < 1 ? 'a '.(round($km * 10) * 100).' m' : 'a '.number_format($km, $km < 10 ? 1 : 0, ',', '.').' km';
@endphp
{{-- Ubicación («Cerca de mí»): el navegador la pide al pulsar y solo se usa para ordenar, no se guarda --}}
<div x-data="{
        estado: '',
        ubicar() {
            if (! navigator.geolocation) { this.estado = 'error'; return; }
            this.estado = 'buscando';
            navigator.geolocation.getCurrentPosition(
                p => { this.estado = ''; $wire.cerca(p.coords.latitude, p.coords.longitude); },
                () => { this.estado = 'error'; },
                { enableHighAccuracy: false, timeout: 10000, maximumAge: 300000 },
            );
        },
     }"
     @if ($urgencias && $lat === null) x-init="ubicar()" @endif>
    @php
        // Filtros secundarios: van plegados para que en el móvil se vean los resultados
        $extra = ($provincia !== '') + ($comarca !== '') + ($municipio !== '') + ($admite !== '') + (int) $verificados + $nPracticos;
        // En la portada las categorías van como accesos grandes; la fila de botones solo aparece al buscar
        $portada = ! $hayFiltros && $sitios->onFirstPage() && $vista === 'lista' && $lat === null;
        $zona = $unaSola ? $provincias->first()->nombre : null;
    @endphp

    @if ($portada)
        @php
            // Cifras que dan confianza, los últimos sitios confirmados y los pueblos más buscados
            $totalSitios = $conteoTipos->sum();
            $confirmados = Sitio::publicados()->where('verificado', true)->count();
            $pueblos = Sitio::publicados()->distinct()->count('municipio_id');
            $recientes = Sitio::publicados()->where('verificado', true)
                ->with(['municipio:id,nombre', 'fotos' => fn ($f) => $f->publicadas()])
                ->orderByDesc('fecha_comprobacion')->take(6)->get();
            $destinos = collect([
                'Cádiz' => 'Cádiz', 'Conil' => 'Conil de la Frontera', 'Tarifa' => 'Tarifa', 'Chiclana' => 'Chiclana de la Frontera',
                'El Puerto' => 'El Puerto de Santa María', 'Vejer' => 'Vejer de la Frontera', 'Jerez' => 'Jerez de la Frontera', 'Sanlúcar' => 'Sanlúcar de Barrameda',
            ])->map(function (string $nombre, string $corto) use ($fotosPortada) {
                $m = \App\Models\Municipio::where('nombre', $nombre)->withCount(['sitios' => fn ($q) => $q->publicados()])->first();

                return [
                    'corto' => $corto,
                    'municipio' => $m,
                    // Si alguna foto de la portada es de ese pueblo, se usa en su tarjeta
                    'foto' => $fotosPortada->first(fn ($f) => $f->lugar && str_contains(mb_strtolower($f->lugar), mb_strtolower($corto))),
                ];
            })->filter(fn ($d) => $d['municipio'] && $d['municipio']->sitios_count > 0)->values();
        @endphp

        {{-- 1. Primera impresión: las fotos de portada de lado a lado y la pregunta en grande encima.
             Pasan solas cada 5 s y se deslizan con el dedo. Las fotos propias van sin crédito; las de otros lo llevan --}}
        <section x-data="{
                    i: 0,
                    total: {{ max(1, $fotosPortada->count()) }},
                    parado: matchMedia('(prefers-reduced-motion: reduce)').matches,
                    ir(n) { this.i = (n + this.total) % this.total; this.$refs.pista.scrollTo({ left: this.i * this.$refs.pista.clientWidth, behavior: 'smooth' }); },
                    init() { this.reloj = setInterval(() => { if (! this.parado && ! document.hidden && this.total > 1) this.ir(this.i + 1); }, 5000); },
                    destroy() { clearInterval(this.reloj); },
                 }"
                 @pointerdown="parado = true" @focusin="parado = true"
                 class="relative isolate overflow-hidden bg-tinta" aria-roledescription="carrusel" aria-label="Fotos de mascotas por Cádiz">
            <ul x-ref="pista" @scroll.debounce.100ms="i = Math.round($el.scrollLeft / $el.clientWidth)"
                class="absolute inset-0 -z-10 flex snap-x snap-mandatory overflow-x-auto [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                @forelse ($fotosPortada as $n => $f)
                    <li class="relative size-full flex-none snap-center" aria-roledescription="foto" aria-label="{{ $n + 1 }} de {{ $fotosPortada->count() }}">
                        <img src="{{ $f->url() }}" alt="{{ $f->descripcion }}" @if ($n === 0) fetchpriority="high" @else loading="lazy" @endif class="size-full object-cover object-[50%_30%]">
                        @if ($f->origen !== 'propia')
                            <span class="absolute right-2 top-2 rounded-md bg-tinta/60 px-2 py-0.5 text-xs text-white">{{ $f->credito()['texto'] }}</span>
                        @endif
                    </li>
                @empty
                    <li class="size-full flex-none"><img src="{{ asset('hero.jpg') }}" alt="Perro con correa sobre una mesa de madera en un parque" fetchpriority="high" class="size-full object-cover object-[50%_25%]"></li>
                @endforelse
            </ul>
            {{-- Degradado para que el texto se lea sobre cualquier foto --}}
            <div class="pointer-events-none absolute inset-0 -z-10 bg-gradient-to-t from-tinta via-tinta/55 to-tinta/10 md:bg-gradient-to-r md:from-tinta/90 md:via-tinta/50 md:to-transparent"></div>

            <div class="pointer-events-none mx-auto flex min-h-[470px] max-w-6xl flex-col justify-end px-4 pb-24 pt-40 md:min-h-[560px] md:justify-center md:pb-28">
                <p class="mb-3 inline-flex w-fit items-center gap-1.5 rounded-full bg-white/15 px-3 py-1 text-sm font-bold text-white ring-1 ring-white/30 backdrop-blur">
                    @svg('heroicon-s-map-pin', 'size-4 text-albero', ['aria-hidden' => 'true']){{ $zona ? 'Provincia de '.$zona : 'Guía de sitios' }} · guía gratuita
                </p>
                <h1 class="max-w-2xl text-[2.6rem] font-extrabold leading-[1.05] text-white sm:text-6xl md:text-7xl">¿Puedo ir con mi mascota?</h1>
                <p class="mt-4 max-w-xl text-lg leading-snug text-white/90 md:text-xl">Hoteles, bares, playas y planes que admiten mascotas, con sus condiciones explicadas antes de que llegues.</p>
            </div>

            @if ($fotosPortada->count() > 1)
                <div class="absolute inset-x-0 bottom-20 flex justify-center gap-1.5 md:bottom-24">
                    @foreach ($fotosPortada as $n => $f)
                        <button type="button" @click="ir({{ $n }})" class="h-2 rounded-full transition-all" :class="i === {{ $n }} ? 'w-6 bg-white' : 'w-2 bg-white/60'" :aria-current="i === {{ $n }}">
                            <span class="sr-only">Ver foto {{ $n + 1 }}</span>
                        </button>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- 2. El buscador, en una tarjeta que se monta sobre la foto. Busca al pulsar (no mientras escribes) --}}
        <section class="relative z-10 mx-auto -mt-16 max-w-6xl px-4">
            <form wire:submit="buscar" role="search"
                  class="grid gap-3 rounded-3xl bg-white p-4 shadow-xl ring-1 ring-linea md:max-w-3xl md:grid-cols-[minmax(0,1fr)_auto_auto] md:p-5">
                <label for="q-portada" class="sr-only">¿A qué pueblo o sitio quieres ir?</label>
                <div class="relative">
                    @svg('heroicon-o-magnifying-glass', 'pointer-events-none absolute left-4 top-1/2 size-6 -translate-y-1/2 text-pino', ['aria-hidden' => 'true'])
                    <input id="q-portada" type="search" wire:model="q" placeholder="¿A qué pueblo o sitio vas?" class="campo min-h-14 rounded-2xl border-2 bg-cal pl-12 text-lg focus:bg-white">
                </div>
                <button type="submit" class="btn btn-pri min-h-14 rounded-2xl px-7 text-lg shadow-sm">@svg('heroicon-o-magnifying-glass', 'size-5', ['aria-hidden' => 'true'])Buscar</button>
                <button type="button" @click="ubicar()" :disabled="estado === 'buscando'" class="btn min-h-14 rounded-2xl px-5 text-lg">
                    @svg('heroicon-o-map-pin', 'size-5', ['aria-hidden' => 'true'])
                    <span x-text="estado === 'buscando' ? 'Buscando…' : 'Cerca de mí'">Cerca de mí</span>
                </button>
                <p x-show="estado === 'error'" x-cloak class="text-sm text-mal md:col-span-3" role="alert">No hemos podido saber dónde estás. Activa la ubicación en el móvil o escribe un pueblo.</p>
            </form>

            {{-- Las cifras que dan confianza, justo debajo del buscador --}}
            <ul class="mt-4 grid grid-cols-3 gap-2 text-center md:max-w-3xl">
                <li class="rounded-2xl bg-white px-2 py-3 ring-1 ring-linea">
                    <span class="block font-display text-2xl font-extrabold text-pino">{{ $totalSitios }}</span>
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

        {{-- 3. Qué buscas: accesos con el color de cada categoría y urgencias en rojo --}}
        <section class="mx-auto max-w-6xl px-4 pt-12">
            <h2 class="text-2xl font-extrabold md:text-3xl">¿Qué buscas?</h2>
            <div class="mt-4 grid grid-cols-2 gap-2 md:grid-cols-4">
                @foreach (Sitio::PRINCIPALES as $clave)
                    <button type="button" wire:click="$set('tipo', @js($clave))"
                        class="flex min-h-14 items-center gap-2.5 rounded-xl px-3 py-2 text-left ring-1 ring-black/5 transition hover:shadow-sm"
                        style="background-color: {{ Sitio::TIPOS[$clave][1] }}14">
                        <span class="grid size-9 flex-none place-items-center rounded-lg bg-white shadow-sm"><x-icono-tipo :tipo="$clave" class="size-5" /></span>
                        <span class="leading-tight">
                            <span class="block font-bold text-tinta">{{ Sitio::TIPOS[$clave][0] }}</span>
                            <span class="text-sm text-gris">{{ $conteoTipos[$clave] ?? 0 }} sitios</span>
                        </span>
                    </button>
                @endforeach
            </div>
            <div class="-mx-4 mt-2 flex gap-2 overflow-x-auto px-4 pb-1 [scrollbar-width:none] md:mx-0 md:flex-wrap md:px-0">
                @foreach (array_diff(array_keys(Sitio::TIPOS), Sitio::PRINCIPALES) as $clave)
                    <button type="button" wire:click="$set('tipo', @js($clave))" class="chip min-h-11"><x-icono-tipo :tipo="$clave" />{{ Sitio::TIPOS[$clave][0] }}</button>
                @endforeach
            </div>
            {{-- Urgencias: en rojo y a todo lo ancho, para encontrarlas al momento --}}
            <a href="{{ route('urgencias') }}" wire:navigate class="mt-3 flex min-h-14 items-center gap-3 rounded-xl bg-mal px-4 py-3 text-white no-underline shadow-sm transition hover:brightness-110 md:max-w-xl">
                @svg('heroicon-o-lifebuoy', 'size-7 flex-none', ['aria-hidden' => 'true'])
                <span class="leading-tight">
                    <span class="block font-bold">Urgencias veterinarias 24 h</span>
                    <span class="text-sm text-white/90">Las más cercanas, con botón para llamar</span>
                </span>
            </a>
        </section>

        {{-- 4. Recién confirmados: sitios que nos han contestado ellos mismos --}}
        @if ($recientes->isNotEmpty())
            <section class="mx-auto max-w-6xl px-4 pt-12">
                <h2 class="text-2xl font-extrabold md:text-3xl">Confirmados por el propio sitio</h2>
                <p class="mt-1 text-gris">Nos lo han dicho ellos. Los últimos en contestar:</p>
                <ul class="-mx-4 mt-4 flex snap-x gap-3 overflow-x-auto px-4 pb-3 [scrollbar-width:none] md:mx-0 md:grid md:grid-cols-3 md:overflow-visible md:px-0">
                    @foreach ($recientes as $r)
                        <li wire:key="reciente-{{ $r->id }}" class="flex w-72 flex-none snap-start md:w-auto"><x-tarjeta-sitio :sitio="$r" nivel="h3" /></li>
                    @endforeach
                </ul>
            </section>
        @endif

        {{-- 5. Pueblos más buscados --}}
        @if ($destinos->isNotEmpty())
            <section class="mx-auto max-w-6xl px-4 pt-10">
                <h2 class="text-2xl font-extrabold md:text-3xl">Elige un pueblo</h2>
                <p class="mt-1 text-gris">Todo lo que admite mascotas en cada sitio.</p>
                <ul class="-mx-4 mt-4 flex snap-x gap-3 overflow-x-auto px-4 pb-2 [scrollbar-width:none] md:mx-0 md:grid md:grid-cols-4 md:overflow-visible md:px-0">
                    @foreach ($destinos as $d)
                        <li class="w-40 flex-none snap-start md:w-auto">
                            <button type="button" wire:click="$set('municipio', '{{ $d['municipio']->id }}')" class="group relative block aspect-[4/5] w-full overflow-hidden rounded-2xl bg-pino text-left shadow-sm md:aspect-[4/3]">
                                @if ($d['foto'])
                                    <img src="{{ $d['foto']->url() }}" alt="" loading="lazy" class="absolute inset-0 size-full object-cover transition group-hover:scale-105">
                                @else
                                    <span class="absolute inset-0 bg-gradient-to-br from-pino to-pino-oscuro"></span>
                                    @svg('heroicon-o-map-pin', 'absolute -right-4 -top-4 size-32 text-white/10', ['aria-hidden' => 'true'])
                                @endif
                                <span class="absolute inset-0 bg-gradient-to-t from-tinta/80 to-transparent"></span>
                                <span class="absolute inset-x-0 bottom-0 p-3 text-white">
                                    <span class="block font-display text-xl font-extrabold leading-tight">{{ $d['corto'] }}</span>
                                    <span class="text-sm text-white/90">{{ $d['municipio']->sitios_count }} sitios</span>
                                </span>
                            </button>
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
            <a href="{{ route('como-funciona') }}" wire:navigate class="mt-3 inline-flex min-h-11 items-center gap-2 font-bold text-pino underline">@svg('heroicon-o-question-mark-circle', 'size-5', ['aria-hidden' => 'true'])Cómo comprobamos cada dato</a>
        </section>

        <h2 class="mx-auto max-w-6xl px-4 pb-3 pt-12 text-2xl font-extrabold md:text-3xl">Todos los sitios</h2>
    @endif

    {{-- Lista: un título que dice qué estás viendo y cuántos sitios hay --}}
    @if (! $portada && ! $urgencias)
        @php
            $tituloLista = match (true) {
                $municipio !== '' && isset($municipios[(int) $municipio]) => 'Con tu mascota en '.$municipios[(int) $municipio],
                $tipo !== '' => Sitio::TIPOS[$tipo][0].' con tu mascota',
                $q !== '' => 'Resultados para «'.$q.'»',
                default => 'Sitios que admiten mascotas',
            };
        @endphp
        <section class="bg-pino text-white">
            <div class="mx-auto max-w-6xl px-4 pb-5 pt-6">
                <h1 class="text-3xl font-extrabold leading-tight md:text-4xl">{{ $tituloLista }}</h1>
                <p class="mt-1 text-white/85"><strong class="text-white">{{ $sitios->total() }}</strong> {{ $sitios->total() === 1 ? 'sitio' : 'sitios' }}{{ $zona ? ' en la provincia de '.$zona : '' }}</p>
            </div>
        </section>
    @endif

    {{-- Buscador y filtros: fijo solo lo imprescindible (buscar, lista/mapa y categorías) --}}
    <section x-data="{ filtros: false }" class="sticky top-[57px] z-20 border-b border-linea bg-white">
        <div class="mx-auto grid max-w-6xl gap-3 px-4 py-3">
            <div class="grid gap-3">
            @unless ($portada)
            <div class="flex gap-2">
                <label for="q" class="sr-only">Buscar</label>
                <div class="relative flex-1">
                    @svg('heroicon-o-magnifying-glass', 'pointer-events-none absolute left-3 top-1/2 size-5 -translate-y-1/2 text-gris', ['aria-hidden' => 'true'])
                    <input id="q" type="search" wire:model.live.debounce.300ms="q" placeholder="Pueblo o sitio" class="campo pl-10">
                </div>
                <x-vista-toggle :vista="$vista" />
            </div>
            @endunless

            @unless ($portada)
            <div class="-mx-4 flex gap-2 overflow-x-auto px-4 pb-1 [scrollbar-width:none] md:mx-0 md:flex-wrap md:overflow-visible md:px-0">
                <button type="button" class="chip" wire:click="$set('tipo', '')" aria-pressed="{{ $tipo === '' ? 'true' : 'false' }}">Todo</button>
                @foreach (Sitio::TIPOS as $clave => [$etiqueta])
                    <button type="button" class="chip" wire:click="$set('tipo', @js($clave))" aria-pressed="{{ $tipo === $clave ? 'true' : 'false' }}">
                        <x-icono-tipo :tipo="$clave" />
                        {{ $etiqueta }} <span class="font-normal opacity-70">{{ $conteoTipos[$clave] ?? 0 }}</span>
                    </button>
                @endforeach
            </div>
            @endunless

            @unless ($portada)
            <div class="flex flex-wrap items-center gap-2">
                @if ($lat === null)
                    <button type="button" @click="ubicar()" :disabled="estado === 'buscando'" class="btn min-h-10 py-1.5">
                        @svg('heroicon-o-map-pin', 'size-5', ['aria-hidden' => 'true'])
                        <span x-text="estado === 'buscando' ? 'Buscando dónde estás…' : 'Cerca de mí'">Cerca de mí</span>
                    </button>
                @else
                    <span class="sello bg-pino-suave px-3 py-2 text-sm text-pino">@svg('heroicon-s-map-pin', 'size-4', ['aria-hidden' => 'true'])Ordenado por cercanía</span>
                    <button type="button" wire:click="sinUbicacion" class="inline-flex items-center gap-1 text-sm font-bold text-pino underline">@svg('heroicon-m-x-mark', 'size-4', ['aria-hidden' => 'true'])Quitar</button>
                @endif
                <p x-show="estado === 'error'" x-cloak class="text-sm text-mal" role="alert">No hemos podido saber dónde estás. Activa la ubicación en el móvil o busca un pueblo.</p>
            </div>
            @endunless
            </div>

            <div class="flex flex-wrap items-center gap-x-3 gap-y-2 text-sm text-gris" aria-live="polite">
                <button type="button" @click="filtros = !filtros" :aria-expanded="filtros" aria-controls="mas-filtros"
                    class="inline-flex min-h-10 items-center gap-2 rounded-full border px-3 font-bold {{ $extra ? 'border-pino bg-pino-suave text-pino' : 'border-linea bg-white text-tinta' }}">
                    @svg('heroicon-o-adjustments-horizontal', 'size-5', ['aria-hidden' => 'true'])
                    Filtros{{ $extra ? ' ('.$extra.')' : '' }}
                    @svg('heroicon-m-chevron-down', 'size-4 transition', ['aria-hidden' => 'true', ':class' => "filtros && 'rotate-180'"])
                </button>
                <span><strong class="text-tinta">{{ $sitios->total() }}</strong> {{ $sitios->total() === 1 ? 'sitio' : 'sitios' }}@if ($vista === 'mapa'), {{ count($puntos) }} en el mapa @endif</span>
                @if ($hayFiltros)
                    <button type="button" wire:click="limpiar" class="inline-flex items-center gap-1 font-bold text-pino underline">@svg('heroicon-m-x-mark', 'size-4', ['aria-hidden' => 'true'])Quitar filtros</button>
                @endif
                <span wire:loading class="text-pino">Buscando…</span>
                @if ($portada)
                    <span class="ml-auto"><x-vista-toggle :vista="$vista" /></span>
                @endif
            </div>

            <div id="mas-filtros" x-show="filtros" x-cloak x-collapse class="grid gap-2 sm:grid-cols-4">
                @unless ($unaSola)
                    <select wire:model.live="provincia" class="campo" aria-label="Provincia">
                        <option value="">Todas las provincias</option>
                        @foreach ($provincias as $p)
                            <option value="{{ $p->slug }}">{{ $p->nombre }}</option>
                        @endforeach
                    </select>
                @endunless
                @if ($comarcas->isNotEmpty())
                    <select wire:model.live="comarca" class="campo" aria-label="Comarca">
                        <option value="">Todas las comarcas</option>
                        @foreach ($comarcas as $c)
                            <option value="{{ $c }}">{{ $c }}</option>
                        @endforeach
                    </select>
                @endif
                @if ($municipios->isNotEmpty())
                    <select wire:model.live="municipio" class="campo" aria-label="Municipio">
                        <option value="">Todos los municipios</option>
                        @foreach ($municipios as $id => $nombre)
                            <option value="{{ $id }}">{{ $nombre }}</option>
                        @endforeach
                    </select>
                @endif
                <select wire:model.live="admite" class="campo" aria-label="Admite mascotas">
                    <option value="">Admitan o no mascotas</option>
                    <option value="si">Admiten mascotas</option>
                    <option value="condiciones">Admiten, con condiciones</option>
                    <option value="no">No admiten mascotas</option>
                </select>
                <label class="flex min-h-11 items-center gap-2 rounded-xl border border-linea bg-white px-3 font-bold">
                    <input type="checkbox" wire:model.live="verificados" class="size-5 accent-pino">
                    Solo confirmados por el sitio
                </label>
                <fieldset class="grid gap-2 sm:col-span-4 sm:grid-cols-2 lg:grid-cols-3">
                    <legend class="etiqueta">Lo que necesitas</legend>
                    @foreach ($practicos as $campo => $texto)
                        <label class="flex min-h-11 items-center gap-2 rounded-xl border border-linea bg-white px-3 py-2 leading-tight">
                            <input type="checkbox" wire:model.live="{{ $campo }}" class="size-5 flex-none accent-pino">
                            {{ $texto }}
                        </label>
                    @endforeach
                    <p class="text-sm text-gris sm:col-span-full">Solo salen los sitios donde lo sabemos seguro. Si falta alguno, puede que aún no lo hayamos comprobado.</p>
                </fieldset>
            </div>
        </div>
    </section>

    {{-- Mapa: se crea una vez y se actualiza con cada búsqueda --}}
    <div wire:ignore
         x-data="mapaExplorar(@js($mapaCfg), @js($puntos))"
         x-on:puntos.window="pintar($event.detail.puntos)"
         x-effect="$wire.vista === 'mapa' && redimensionar()"
         x-show="$wire.vista === 'mapa'"
         @if ($vista !== 'mapa') style="display: none" @endif
         class="relative h-[calc(100dvh-230px)] min-h-[420px]">
        <div x-ref="mapa" class="h-full w-full"></div>
    </div>

    @if ($vista === 'lista')
        <section class="mx-auto max-w-6xl px-4 py-6">
            @if ($urgencias)
                <div class="mb-4 flex gap-3 rounded-2xl border-2 border-mal bg-mal-suave p-4 text-mal" role="note">
                    @svg('heroicon-o-lifebuoy', 'size-7 flex-none', ['aria-hidden' => 'true'])
                    <div>
                        <h1 class="font-display text-xl font-extrabold">Urgencias veterinarias 24 h</h1>
                        <p class="mt-1 text-tinta">Llama antes de ir para que te esperen. {{ $lat === null ? 'Pulsa «Cerca de mí» para ver primero las más cercanas.' : 'Están ordenadas de la más cercana a la más lejana.' }}</p>
                    </div>
                </div>
            @endif
            {{-- Filtros rápidos de lo que más se pregunta, a un toque (los mismos que hay dentro de «Filtros») --}}
            @unless ($urgencias)
                <div class="-mx-4 mb-5 flex gap-2 overflow-x-auto px-4 pb-1 [scrollbar-width:none] md:mx-0 md:flex-wrap md:px-0">
                    @foreach (['verificados' => ['check-badge', 'Solo confirmados'], 'gratis' => ['banknotes', 'Sin suplemento'], 'grandes' => ['scale', 'Perros grandes'], 'dentro' => ['home', 'Puede entrar dentro']] as $campo => [$icono, $texto])
                        <button type="button" wire:click="$toggle('{{ $campo }}')" aria-pressed="{{ $this->{$campo} ? 'true' : 'false' }}"
                            class="inline-flex min-h-10 flex-none items-center gap-1.5 rounded-full border px-3 text-sm font-bold {{ $this->{$campo} ? 'border-pino bg-pino text-white' : 'border-pino/30 bg-pino-suave text-pino' }}">
                            @svg('heroicon-o-'.$icono, 'size-4', ['aria-hidden' => 'true']){{ $texto }}
                        </button>
                    @endforeach
                </div>
            @endunless
            @if ($sitios->isEmpty())
                <div class="tarjeta mx-auto max-w-lg p-8 text-center">
                    <p class="text-lg font-bold">No hay sitios con estos filtros.</p>
                    <p class="mt-1 text-gris">Prueba a quitar algún filtro o a buscar otro nombre. Si conoces un sitio que falte, cuéntanoslo.</p>
                    <div class="mt-4 flex justify-center gap-2">
                        <button type="button" wire:click="limpiar" class="btn">@svg('heroicon-o-x-mark', 'size-5', ['aria-hidden' => 'true'])Quitar filtros</button>
                        <a href="{{ route('sugerir') }}" wire:navigate class="btn btn-pri">@svg('heroicon-o-light-bulb', 'size-5', ['aria-hidden' => 'true'])Sugerir un sitio</a>
                    </div>
                </div>
            @else
                <p class="mb-3 text-sm leading-7 text-gris"><x-confirmado class="mr-1 align-middle" />nos lo ha dicho el propio sitio. Lo demás, confírmalo antes de ir.</p>
                <ul class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($sitios as $s)
                        <li wire:key="s-{{ $s->id }}" class="flex flex-col">
                            <x-tarjeta-sitio :sitio="$s" :distancia="isset($s->distancia_km) ? $distancia($s->distancia_km) : null" />
                            {{-- En una urgencia lo primero es llamar: sin tener que abrir la ficha --}}
                            @if ($s->urgencias_24h && ($tel = $s->telefonoUrgencias()))
                                <a href="tel:{{ $tel }}" class="btn btn-pri mt-2 w-full">@svg('heroicon-o-phone', 'size-5', ['aria-hidden' => 'true'])Llamar a urgencias</a>
                            @endif
                        </li>
                    @endforeach
                </ul>

                {{ $sitios->links() }}
            @endif
        </section>
    @endif

    @if ($portada)
        {{-- Cierre de la portada: invitación a sugerir, con el color de la marca --}}
        <section class="mx-auto max-w-6xl px-4 pb-12">
            <div class="relative overflow-hidden rounded-3xl bg-pino p-6 text-white md:flex md:items-center md:justify-between md:p-10">
                @svg('heroicon-o-light-bulb', 'absolute -right-6 -top-6 size-40 text-white/10', ['aria-hidden' => 'true'])
                <div class="relative">
                    <h2 class="text-2xl font-extrabold md:text-3xl">¿Conoces un sitio que falta?</h2>
                    <p class="mt-1 text-white/85">Cuéntanoslo y lo comprobamos antes de publicarlo.</p>
                </div>
                <a href="{{ route('sugerir') }}" wire:navigate class="btn btn-albero relative mt-4 w-full md:mt-0 md:w-auto">@svg('heroicon-o-light-bulb', 'size-5', ['aria-hidden' => 'true'])Sugerir un sitio</a>
            </div>
        </section>
    @endif
</div>
