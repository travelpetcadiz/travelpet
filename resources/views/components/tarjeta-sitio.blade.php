{{-- Tarjeta de un sitio en una lista: la respuesta primero, luego el nombre, la categoría y las condiciones --}}
@props(['sitio', 'distancia' => null, 'nivel' => 'h2'])
<a href="{{ route('sitio', $sitio) }}" wire:navigate class="tarjeta flex flex-1 flex-col gap-2 p-4 no-underline transition hover:border-gris hover:shadow-sm">
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
    <{{ $nivel }} class="text-lg font-semibold leading-snug">{{ $sitio->nombre }}</{{ $nivel }}>
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
</a>
