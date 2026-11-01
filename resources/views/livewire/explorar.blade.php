@php
    use App\Models\Sitio;
    $hayFiltros = $q !== '' || $tipo !== '' || $provincia !== '' || $comarca !== '' || $municipio !== '' || $admite !== '' || $verificados;
@endphp
<div>
    @php
        // Filtros secundarios: van plegados para que en el móvil se vean los resultados
        $extra = ($provincia !== '') + ($comarca !== '') + ($municipio !== '') + ($admite !== '') + (int) $verificados;
        $atajos = [
            'Alojamiento' => 'Dormir',
            'Restauración' => 'Comer y beber',
            'Playa' => 'Playas',
            'Espacio' => 'Parques',
            'Salud' => 'Veterinarios',
            'Servicios' => 'Servicios para perros',
        ];
        $zona = $unaSola ? $provincias->first()->nombre : null;
    @endphp

    @if (! $hayFiltros && $sitios->onFirstPage() && $vista === 'lista')
        <section class="bg-pino-suave">
            <div class="mx-auto grid max-w-6xl items-center gap-6 px-4 pb-6 pt-5 md:grid-cols-[minmax(0,1fr)_300px] md:gap-10 md:py-10">
                <div class="grid gap-4">
                    <div>
                        <h1 class="text-4xl font-extrabold leading-tight text-tinta sm:text-5xl">¿Puedo ir con mi perro?</h1>
                        <p class="mt-2 max-w-xl text-lg text-gris">
                            Busca un hotel, un bar, una playa o un parque{{ $zona ? ' de '.$zona : '' }} y mira si admiten perros y con qué condiciones.
                        </p>
                    </div>
                    <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                        @foreach ($atajos as $clave => $texto)
                            <button type="button" wire:click="$set('tipo', @js($clave))"
                                class="flex min-h-14 items-center gap-3 rounded-2xl border border-linea bg-white px-4 py-3 text-left font-bold hover:border-pino">
                                <span class="size-3 flex-none rounded-full" style="background: {{ Sitio::TIPOS[$clave][1] }}"></span>
                                <span class="leading-tight">{{ $texto }}<span class="block text-sm font-normal text-gris">{{ $conteoTipos[$clave] ?? 0 }} sitios</span></span>
                            </button>
                        @endforeach
                    </div>
                </div>
                {{-- Foto vertical: al lado del texto en escritorio, banda recortada por arriba en móvil --}}
                <img src="{{ asset('hero.jpg') }}" alt="Perro con correa sobre una mesa de madera en un parque"
                     class="order-first h-36 w-full rounded-3xl object-cover object-[50%_12%] shadow-sm md:order-none md:h-auto md:aspect-[4/5]">
            </div>
        </section>
    @endif

    {{-- Buscador y filtros: fijo solo lo imprescindible (buscar, lista/mapa y categorías) --}}
    <section x-data="{ filtros: false }" class="sticky top-[57px] z-20 border-b border-linea bg-white">
        <div class="mx-auto grid max-w-6xl gap-3 px-4 py-3">
            <div class="flex gap-2">
                <label for="q" class="sr-only">Buscar</label>
                <input id="q" type="search" wire:model.live.debounce.300ms="q" placeholder="Pueblo o sitio" class="campo flex-1">
                <div class="flex rounded-full border border-linea bg-cal p-1" role="group" aria-label="Ver como">
                    <button type="button" wire:click="$set('vista', 'lista')" aria-pressed="{{ $vista === 'lista' ? 'true' : 'false' }}"
                        class="rounded-full px-3 text-sm font-bold sm:px-4 {{ $vista === 'lista' ? 'bg-pino text-white' : 'text-gris' }}">Lista</button>
                    <button type="button" wire:click="$set('vista', 'mapa')" aria-pressed="{{ $vista === 'mapa' ? 'true' : 'false' }}"
                        class="rounded-full px-3 text-sm font-bold sm:px-4 {{ $vista === 'mapa' ? 'bg-pino text-white' : 'text-gris' }}">Mapa</button>
                </div>
            </div>

            <div class="-mx-4 flex gap-2 overflow-x-auto px-4 pb-1 [scrollbar-width:none] md:mx-0 md:flex-wrap md:overflow-visible md:px-0">
                <button type="button" class="chip" wire:click="$set('tipo', '')" aria-pressed="{{ $tipo === '' ? 'true' : 'false' }}">Todo</button>
                @foreach (Sitio::TIPOS as $clave => [$etiqueta, $color])
                    <button type="button" class="chip" wire:click="$set('tipo', @js($clave))" aria-pressed="{{ $tipo === $clave ? 'true' : 'false' }}">
                        <span class="size-2.5 rounded-full" style="background: {{ $color }}"></span>
                        {{ $etiqueta }} <span class="font-normal opacity-70">{{ $conteoTipos[$clave] ?? 0 }}</span>
                    </button>
                @endforeach
            </div>

            <div class="flex flex-wrap items-center gap-x-3 gap-y-2 text-sm text-gris" aria-live="polite">
                <button type="button" @click="filtros = !filtros" :aria-expanded="filtros" aria-controls="mas-filtros"
                    class="inline-flex min-h-10 items-center gap-2 rounded-full border px-3 font-bold {{ $extra ? 'border-pino bg-pino-suave text-pino' : 'border-linea bg-white text-tinta' }}">
                    Filtros{{ $extra ? ' ('.$extra.')' : '' }}
                    <span aria-hidden="true" class="transition" :class="filtros && 'rotate-180'">▾</span>
                </button>
                <span><strong class="text-tinta">{{ $sitios->total() }}</strong> {{ $sitios->total() === 1 ? 'sitio' : 'sitios' }}@if ($vista === 'mapa'), {{ count($puntos) }} en el mapa @endif</span>
                @if ($hayFiltros)
                    <button type="button" wire:click="limpiar" class="font-bold text-pino underline">Quitar filtros</button>
                @endif
                <span wire:loading class="text-pino">Buscando…</span>
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
                <select wire:model.live="admite" class="campo" aria-label="Admite perros">
                    <option value="">Admitan o no perros</option>
                    <option value="si">Admiten perros</option>
                    <option value="condiciones">Admiten, con condiciones</option>
                    <option value="no">No admiten perros</option>
                </select>
                <label class="flex min-h-11 items-center gap-2 rounded-xl border border-linea bg-white px-3 font-bold">
                    <input type="checkbox" wire:model.live="verificados" class="size-5 accent-pino">
                    Solo confirmados por el sitio
                </label>
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
            @if ($sitios->isEmpty())
                <div class="tarjeta mx-auto max-w-lg p-8 text-center">
                    <p class="text-lg font-bold">No hay sitios con estos filtros.</p>
                    <p class="mt-1 text-gris">Prueba a quitar algún filtro o a buscar otro nombre. Si conoces un sitio que falte, cuéntanoslo.</p>
                    <div class="mt-4 flex justify-center gap-2">
                        <button type="button" wire:click="limpiar" class="btn">Quitar filtros</button>
                        <a href="{{ route('sugerir') }}" wire:navigate class="btn btn-pri">Sugerir un sitio</a>
                    </div>
                </div>
            @else
                <p class="mb-3 text-sm text-gris"><span class="sello bg-ok-suave text-ok">✓ Confirmado</span> nos lo ha dicho el propio sitio. Lo demás, confírmalo antes de ir.</p>
                <ul class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($sitios as $s)
                        <li wire:key="s-{{ $s->id }}">
                            <a href="{{ route('sitio', $s) }}" wire:navigate class="tarjeta flex h-full flex-col gap-2 p-4 no-underline transition hover:border-gris hover:shadow-sm">
                                <span class="flex flex-wrap gap-1.5">
                                    <x-admite :valor="$s->admite_perros" class="px-2.5 py-1 text-sm" />
                                    @if ($s->verificado)
                                        <span class="sello bg-ok-suave px-2.5 py-1 text-sm text-ok" title="Lo ha confirmado el propio sitio">✓ Confirmado</span>
                                    @endif
                                </span>
                                <h2 class="text-lg font-semibold leading-snug">{{ $s->nombre }}</h2>
                                <span class="flex items-center gap-2 text-sm text-gris">
                                    <span class="size-2.5 flex-none rounded-full" style="background: {{ $s->color() }}"></span>
                                    {{ $s->subtipo ?: $s->etiquetaTipo() }}, {{ $s->municipio->nombre }}
                                </span>
                                @if ($s->otras_condiciones || $s->zonas_permitidas)
                                    <p class="line-clamp-2 text-sm text-gris">{{ $s->zonas_permitidas ?: $s->otras_condiciones }}</p>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>

                <div class="mt-6">{{ $sitios->links() }}</div>
            @endif
        </section>
    @endif
</div>
