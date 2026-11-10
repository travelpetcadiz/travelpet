{{-- Tarjeta de un sitio en una lista: la respuesta primero, luego el nombre, la categoría y las condiciones.
     Arriba, la foto del sitio si la hay; si no, una franja fina con el color de su categoría.
     Toda la tarjeta lleva a la ficha (el enlace del nombre la cubre) salvo el botón «Quiero ir», que guarda en Mi viaje --}}
@props(['sitio', 'distancia' => null, 'nivel' => 'h2'])
@php
    $s = $sitio;
    $foto = $s->relationLoaded('fotos') ? $s->fotos->first() : null;
    // Lo práctico que sabemos seguro, con icono (null = no lo sabemos y no sale)
    $datos = array_filter([
        $s->sin_suplemento === null ? null : ['banknotes', $s->sin_suplemento ? 'Sin suplemento' : 'Con suplemento'],
        $s->admite_grandes === null ? null : ['scale', $s->admite_grandes ? 'Perros grandes' : 'Sin perros grandes'],
        $s->puede_entrar === null ? null : ['home', $s->puede_entrar ? 'Puede entrar dentro' : 'Solo terraza'],
        $s->sin_correa === null ? null : ['bolt', $s->sin_correa ? 'Zona sin correa' : 'Con correa'],
    ]);
    $estadoHoy = $s->hoy();
@endphp
<div class="relative flex flex-1 flex-col overflow-hidden rounded-2xl border border-linea bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
    @if ($foto)
        <img src="{{ $foto->url() }}" alt="" loading="lazy" class="h-32 w-full object-cover">
    @else
        <span class="block h-1.5 flex-none" style="background-color: {{ $s->color() }}" aria-hidden="true"></span>
    @endif

    <div class="flex flex-1 flex-col gap-2 p-4">
        <span class="flex flex-wrap gap-1.5">
            {{-- En veterinarios y servicios para mascotas se da por hecho que admiten mascotas --}}
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
        <{{ $nivel }} class="text-lg font-extrabold leading-snug">
            <a href="{{ route('sitio', $s) }}" wire:navigate class="no-underline after:absolute after:inset-0 after:content-['']">{{ $s->nombre }}</a>
        </{{ $nivel }}>
        <span class="flex items-center gap-1.5 text-sm text-gris">
            <x-icono-tipo :tipo="$s->tipo" class="size-4 flex-none" />
            {{ $s->subtipo ?: $s->etiquetaTipo() }} · {{ $s->municipio->nombre }}
        </span>
        @if ($distancia)
            <span class="flex items-center gap-1.5 text-sm font-bold text-pino">@svg('heroicon-o-map-pin', 'size-4 flex-none', ['aria-hidden' => 'true']){{ $distancia }}</span>
        @endif
        @if ($s->esServicio() && $s->temporada)
            <p class="flex items-start gap-2 text-sm text-gris">@svg('heroicon-o-clock', 'mt-0.5 size-4 flex-none', ['aria-hidden' => 'true'])<span class="line-clamp-2">{{ $s->temporada }}</span></p>
        @elseif ($datos)
            <ul class="flex flex-wrap gap-1.5">
                @foreach ($datos as [$icono, $texto])
                    <li class="inline-flex items-center gap-1 rounded-md bg-cal px-2 py-1 text-sm font-bold">@svg('heroicon-o-'.$icono, 'size-4 text-pino', ['aria-hidden' => 'true']){{ $texto }}</li>
                @endforeach
            </ul>
        @elseif ($s->otras_condiciones || $s->zonas_permitidas)
            <p class="line-clamp-2 text-sm text-gris">{{ $s->zonas_permitidas ?: $s->otras_condiciones }}</p>
        @endif

        <div class="mt-auto flex items-center justify-between gap-2 pt-2" x-data="{ id: {{ $s->id }} }">
            <button type="button" @click="$store.viaje.alternar(id, 'quiero')"
                :aria-pressed="$store.viaje.estado(id) === 'quiero'"
                :class="$store.viaje.estado(id) === 'quiero' && 'border-pino bg-pino-suave text-pino'"
                class="btn relative z-10 min-h-10 px-3 py-1 text-sm" aria-pressed="false">
                <span x-show="$store.viaje.estado(id) !== 'quiero'" class="inline-flex items-center gap-2">@svg('heroicon-o-heart', 'size-5', ['aria-hidden' => 'true'])Quiero ir</span>
                <span x-show="$store.viaje.estado(id) === 'quiero'" x-cloak class="inline-flex items-center gap-2">@svg('heroicon-s-heart', 'size-5', ['aria-hidden' => 'true'])Guardado</span>
            </button>
            <span class="inline-flex items-center gap-1.5 text-sm font-bold text-pino">@svg('heroicon-o-list-bullet', 'size-4', ['aria-hidden' => 'true']){{ $s->esServicio() ? 'Ver ficha' : 'Ver condiciones' }}</span>
        </div>
    </div>
</div>
