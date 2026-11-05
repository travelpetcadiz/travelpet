{{-- Tarjeta de un sitio en una lista: la respuesta primero, luego el nombre, la categoría y las condiciones.
     Toda la tarjeta lleva a la ficha (el enlace del nombre la cubre) salvo el botón «Quiero ir», que guarda en Mi viaje --}}
@props(['sitio', 'distancia' => null, 'nivel' => 'h2'])
<div class="tarjeta relative flex flex-1 flex-col gap-2 p-4 transition hover:border-gris hover:shadow-sm">
    <span class="flex flex-wrap gap-1.5">
        {{-- En veterinarios y servicios para mascotas se da por hecho que admiten mascotas --}}
        @if (! $sitio->esServicio() || $sitio->admite_perros === 'no')
            <x-admite :valor="$sitio->admite_perros" class="px-2.5 py-1 text-sm" />
        @endif
        @if ($sitio->verificado)
            <x-confirmado class="px-2.5 py-1 text-sm" />
        @endif
        @if ($sitio->urgencias_24h)
            <span class="sello bg-mal-suave px-2.5 py-1 text-sm text-mal">@svg('heroicon-s-lifebuoy', 'size-4', ['aria-hidden' => 'true'])Urgencias 24 h</span>
        @endif
        @if ($estadoHoy = $sitio->hoy())
            <span class="sello px-2.5 py-1 text-sm {{ $estadoHoy['puede'] ? 'bg-ok-suave text-ok' : 'bg-mal-suave text-mal' }}">@svg('heroicon-s-calendar-days', 'size-4', ['aria-hidden' => 'true']){{ $estadoHoy['puede'] ? 'Hoy sí' : 'Hoy no' }}</span>
        @endif
    </span>
    <{{ $nivel }} class="text-lg font-semibold leading-snug">
        <a href="{{ route('sitio', $sitio) }}" wire:navigate class="no-underline after:absolute after:inset-0 after:rounded-xl after:content-['']">{{ $sitio->nombre }}</a>
    </{{ $nivel }}>
    <span class="flex items-center gap-2 text-sm text-gris">
        <x-icono-tipo :tipo="$sitio->tipo" />
        {{ $sitio->subtipo ?: $sitio->etiquetaTipo() }}, {{ $sitio->municipio->nombre }}
    </span>
    @if ($distancia)
        <span class="flex items-center gap-2 text-sm font-bold text-pino">@svg('heroicon-o-map-pin', 'size-4 flex-none', ['aria-hidden' => 'true']){{ $distancia }}</span>
    @endif
    @if ($sitio->esServicio() && $sitio->temporada)
        <p class="flex items-start gap-2 text-sm text-gris">@svg('heroicon-o-clock', 'mt-0.5 size-4 flex-none', ['aria-hidden' => 'true'])<span class="line-clamp-2">{{ $sitio->temporada }}</span></p>
    @elseif ($sitio->otras_condiciones || $sitio->zonas_permitidas)
        <p class="line-clamp-2 text-sm text-gris">{{ $sitio->zonas_permitidas ?: $sitio->otras_condiciones }}</p>
    @endif
    <div class="mt-auto border-t border-linea pt-3" x-data="{ id: {{ $sitio->id }} }">
        <button type="button" @click="$store.viaje.alternar(id, 'quiero')"
            :aria-pressed="$store.viaje.estado(id) === 'quiero'"
            :class="$store.viaje.estado(id) === 'quiero' && 'border-pino bg-pino-suave text-pino'"
            class="btn relative z-10 min-h-10 px-3 py-1 text-sm" aria-pressed="false">
            <span x-show="$store.viaje.estado(id) !== 'quiero'" class="inline-flex items-center gap-2">@svg('heroicon-o-heart', 'size-5', ['aria-hidden' => 'true'])Quiero ir</span>
            <span x-show="$store.viaje.estado(id) === 'quiero'" x-cloak class="inline-flex items-center gap-2">@svg('heroicon-s-heart', 'size-5', ['aria-hidden' => 'true'])Guardado</span>
        </button>
    </div>
</div>
