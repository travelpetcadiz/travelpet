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
        {{-- Portada: lo primero es buscar. El buscador va dentro y busca al pulsar (no mientras escribes) --}}
        <section class="bg-pino-suave">
            <div class="mx-auto grid max-w-6xl items-center gap-8 px-4 py-8 md:grid-cols-[minmax(0,1fr)_260px] md:gap-12 md:py-12">
                <div class="grid gap-6">
                    <div>
                        <h1 class="text-4xl font-extrabold leading-tight text-tinta sm:text-5xl">¿Puedo ir con mi mascota?</h1>
                        <p class="mt-2 max-w-xl text-lg text-gris">
                            Hoteles, bares, playas y parques{{ $zona ? ' de '.$zona : '' }}: si admiten mascotas y con qué condiciones.
                            <a href="{{ route('como-funciona') }}" wire:navigate class="whitespace-nowrap font-bold text-pino underline">Así funciona</a>
                        </p>
                    </div>

                    <form wire:submit="buscar" role="search" class="grid gap-2 sm:grid-cols-[minmax(0,1fr)_auto_auto]">
                        <label for="q-portada" class="sr-only">¿A qué pueblo o sitio quieres ir?</label>
                        <div class="relative">
                            @svg('heroicon-o-magnifying-glass', 'pointer-events-none absolute left-4 top-1/2 size-6 -translate-y-1/2 text-gris', ['aria-hidden' => 'true'])
                            <input id="q-portada" type="search" wire:model="q" placeholder="¿A qué pueblo o sitio quieres ir?" class="campo min-h-14 rounded-2xl pl-12 text-lg shadow-sm">
                        </div>
                        <button type="submit" class="btn btn-pri min-h-14 rounded-2xl px-6 text-lg">@svg('heroicon-o-magnifying-glass', 'size-5', ['aria-hidden' => 'true'])Buscar</button>
                        <button type="button" @click="ubicar()" :disabled="estado === 'buscando'" class="btn min-h-14 rounded-2xl px-5 text-lg">
                            @svg('heroicon-o-map-pin', 'size-5', ['aria-hidden' => 'true'])
                            <span x-text="estado === 'buscando' ? 'Buscando…' : 'Cerca de mí'">Cerca de mí</span>
                        </button>
                        <p x-show="estado === 'error'" x-cloak class="text-sm text-mal sm:col-span-3" role="alert">No hemos podido saber dónde estás. Activa la ubicación en el móvil o escribe un pueblo.</p>
                    </form>

                    <div class="grid gap-3">
                        <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
                            @foreach (Sitio::PRINCIPALES as $clave)
                                <button type="button" wire:click="$set('tipo', @js($clave))"
                                    class="flex min-h-12 items-center gap-2.5 rounded-xl border border-linea bg-white px-3 py-2 text-left font-bold hover:border-pino">
                                    <x-icono-tipo :tipo="$clave" class="size-6" />
                                    <span class="leading-tight">{{ Sitio::TIPOS[$clave][0] }}<span class="block text-xs font-normal text-gris">{{ $conteoTipos[$clave] ?? 0 }} sitios</span></span>
                                </button>
                            @endforeach
                        </div>
                        <p class="flex flex-wrap items-center gap-x-4 gap-y-2 text-sm">
                            <span class="text-gris">También:</span>
                            @foreach (array_diff(array_keys(Sitio::TIPOS), Sitio::PRINCIPALES) as $clave)
                                <button type="button" wire:click="$set('tipo', @js($clave))" class="inline-flex min-h-9 items-center gap-1.5 font-bold text-tinta underline decoration-linea underline-offset-4 hover:decoration-tinta">
                                    <x-icono-tipo :tipo="$clave" />{{ Sitio::TIPOS[$clave][0] }}
                                </button>
                            @endforeach
                            <a href="{{ route('urgencias') }}" wire:navigate class="inline-flex min-h-9 items-center gap-1.5 font-bold text-mal underline decoration-mal/30 underline-offset-4 hover:decoration-mal">
                                @svg('heroicon-o-lifebuoy', 'size-5', ['aria-hidden' => 'true'])Urgencias veterinarias 24 h
                            </a>
                        </p>
                    </div>
                </div>
                {{-- En el móvil no va la foto: el buscador tiene que verse sin bajar --}}
                <img src="{{ asset('hero.jpg') }}" alt="Perro con correa sobre una mesa de madera en un parque"
                     class="hidden w-full rounded-3xl object-cover object-[50%_12%] shadow-sm md:block md:aspect-[4/5]">
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
                <ul class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($sitios as $s)
                        <li wire:key="s-{{ $s->id }}" class="flex flex-col">
                            <a href="{{ route('sitio', $s) }}" wire:navigate class="tarjeta flex flex-1 flex-col gap-2 p-4 no-underline transition hover:border-gris hover:shadow-sm">
                                <span class="flex flex-wrap gap-1.5">
                                    {{-- En veterinarios y servicios para mascotas se da por hecho que admiten mascotas --}}
                                    @if (! $s->esServicio() || $s->admite_perros === 'no')
                                        <x-admite :valor="$s->admite_perros" class="px-2.5 py-1 text-sm" />
                                    @endif
                                    @if ($s->verificado)
                                        <x-confirmado class="px-2.5 py-1 text-sm" />
                                    @endif
                                    @if ($s->urgencias_24h)
                                        <span class="sello bg-mal-suave px-2.5 py-1 text-sm text-mal">@svg('heroicon-s-lifebuoy', 'size-4', ['aria-hidden' => 'true'])Urgencias 24 h</span>
                                    @endif
                                    @if ($estadoHoy = $s->hoy())
                                        <span class="sello px-2.5 py-1 text-sm {{ $estadoHoy['puede'] ? 'bg-ok-suave text-ok' : 'bg-mal-suave text-mal' }}">@svg('heroicon-s-calendar-days', 'size-4', ['aria-hidden' => 'true']){{ $estadoHoy['puede'] ? 'Hoy sí' : 'Hoy no' }}</span>
                                    @endif
                                </span>
                                <h2 class="text-lg font-semibold leading-snug">{{ $s->nombre }}</h2>
                                <span class="flex items-center gap-2 text-sm text-gris">
                                    <x-icono-tipo :tipo="$s->tipo" />
                                    {{ $s->subtipo ?: $s->etiquetaTipo() }}, {{ $s->municipio->nombre }}
                                </span>
                                @if (isset($s->distancia_km))
                                    <span class="flex items-center gap-2 text-sm font-bold text-pino">@svg('heroicon-o-map-pin', 'size-4 flex-none', ['aria-hidden' => 'true']){{ $distancia($s->distancia_km) }}</span>
                                @endif
                                @if ($s->esServicio() && $s->temporada)
                                    <p class="flex items-start gap-2 text-sm text-gris">@svg('heroicon-o-clock', 'mt-0.5 size-4 flex-none', ['aria-hidden' => 'true'])<span class="line-clamp-2">{{ $s->temporada }}</span></p>
                                @elseif ($s->otras_condiciones || $s->zonas_permitidas)
                                    <p class="line-clamp-2 text-sm text-gris">{{ $s->zonas_permitidas ?: $s->otras_condiciones }}</p>
                                @endif
                            </a>
                            {{-- En una urgencia lo primero es llamar: sin tener que abrir la ficha --}}
                            @if ($s->urgencias_24h && ($tel = $s->telefonoUrgencias()))
                                <a href="tel:{{ $tel }}" class="btn btn-pri mt-2 w-full">@svg('heroicon-o-phone', 'size-5', ['aria-hidden' => 'true'])Llamar a urgencias</a>
                            @endif
                        </li>
                    @endforeach
                </ul>

                <div class="mt-6">{{ $sitios->links() }}</div>
            @endif
        </section>
    @endif
</div>
