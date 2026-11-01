@php
    use App\Models\Sitio;
    $hayFiltros = $q !== '' || $tipo !== '' || $provincia !== '' || $comarca !== '' || $municipio !== '' || $admite !== '' || $verificados;
@endphp
<div>
    @if (! $hayFiltros && $sitios->onFirstPage() && $vista === 'lista')
        <section class="bg-pino-suave">
            <div class="mx-auto grid max-w-6xl items-center gap-6 px-4 py-6 md:grid-cols-[minmax(0,1fr)_300px] md:gap-10 md:py-10">
                <div>
                    <h1 class="text-4xl font-extrabold leading-tight text-tinta sm:text-5xl">Viaja con tu perro, <span class="text-pino">sin sorpresas</span></h1>
                    <p class="mt-3 max-w-xl text-lg text-gris">
                        {{ number_format($conteoTipos->sum(), 0, ',', '.') }} sitios que admiten perros
                        {{ $unaSola ? 'en la provincia de '.$provincias->first()->nombre : 'en '.$provincias->count().' provincias' }},
                        con sus condiciones y de dónde sale cada dato.
                    </p>
                </div>
                {{-- Foto vertical: al lado del texto en escritorio, banda recortada por arriba en móvil --}}
                <img src="{{ asset('hero.jpg') }}" alt="Perro con correa sobre una mesa de madera en un parque"
                     class="order-first h-52 w-full rounded-3xl object-cover object-[50%_12%] shadow-sm md:order-none md:h-auto md:aspect-[4/5]">
            </div>
        </section>
    @endif

    {{-- Buscador y filtros --}}
    <section class="sticky top-[57px] z-20 border-b border-linea bg-white">
        <div class="mx-auto grid max-w-6xl gap-3 px-4 py-3">
            <div class="flex gap-2">
                <label for="q" class="sr-only">Buscar</label>
                <input id="q" type="search" wire:model.live.debounce.300ms="q" placeholder="Busca un sitio, un pueblo o un tipo (hotel, playa…)" class="campo flex-1">
                <div class="flex rounded-full border border-linea bg-cal p-1" role="group" aria-label="Vista">
                    <button type="button" wire:click="$set('vista', 'lista')" aria-pressed="{{ $vista === 'lista' ? 'true' : 'false' }}"
                        class="rounded-full px-4 text-sm font-bold {{ $vista === 'lista' ? 'bg-pino text-white' : 'text-gris' }}">Lista</button>
                    <button type="button" wire:click="$set('vista', 'mapa')" aria-pressed="{{ $vista === 'mapa' ? 'true' : 'false' }}"
                        class="rounded-full px-4 text-sm font-bold {{ $vista === 'mapa' ? 'bg-pino text-white' : 'text-gris' }}">Mapa</button>
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

            <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
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
                    <option value="">Admite perros: todos</option>
                    <option value="si">Sí, sin condiciones</option>
                    <option value="condiciones">Con condiciones</option>
                    <option value="no">No admite</option>
                </select>
                <label class="flex min-h-11 items-center gap-2 rounded-xl border border-linea bg-white px-3 font-bold">
                    <input type="checkbox" wire:model.live="verificados" class="size-5 accent-pino">
                    Solo verificados
                </label>
            </div>

            <div class="flex flex-wrap items-center gap-3 text-sm text-gris" aria-live="polite">
                <span><strong class="text-tinta">{{ $sitios->total() }}</strong> {{ $sitios->total() === 1 ? 'sitio' : 'sitios' }}
                    @if ($vista === 'mapa') · {{ count($puntos) }} en el mapa @endif</span>
                @if ($hayFiltros)
                    <button type="button" wire:click="limpiar" class="font-bold text-pino underline">Quitar filtros</button>
                @endif
                <span wire:loading class="text-pino">Buscando…</span>
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
                <ul class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($sitios as $s)
                        <li wire:key="s-{{ $s->id }}">
                            <a href="{{ route('sitio', $s) }}" wire:navigate class="tarjeta flex h-full flex-col gap-2 p-4 no-underline transition hover:border-gris hover:shadow-sm">
                                <span class="flex items-center gap-2 text-sm font-bold">
                                    <span class="size-2.5 rounded-full" style="background: {{ $s->color() }}"></span>
                                    {{ $s->etiquetaTipo() }}@if ($s->subtipo)<span class="font-normal text-gris">· {{ $s->subtipo }}</span>@endif
                                </span>
                                <h2 class="text-lg font-semibold leading-snug">{{ $s->nombre }}</h2>
                                <span class="text-sm text-gris">{{ $s->municipio->nombre }}</span>
                                @if ($s->otras_condiciones || $s->zonas_permitidas)
                                    <p class="line-clamp-2 text-sm text-gris">{{ $s->otras_condiciones ?: $s->zonas_permitidas }}</p>
                                @endif
                                <span class="mt-auto flex flex-wrap gap-1.5 pt-1">
                                    <x-admite :valor="$s->admite_perros" />
                                    @if ($s->verificado)
                                        <span class="sello bg-ok-suave text-ok">✓ Verificado</span>
                                    @endif
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>

                <div class="mt-6">{{ $sitios->links() }}</div>
            @endif
        </section>
    @endif
</div>
