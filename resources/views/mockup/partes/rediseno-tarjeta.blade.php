{{-- Boceto del rediseño: tarjeta de un sitio.
     Arriba, la foto del sitio o, si no la hay, una franja con el color y el icono de su categoría.
     Luego la respuesta, el nombre, el pueblo y los datos que sabemos seguro (null = no lo sabemos y no sale) --}}
@props(['sitio', 'ancho' => false])
@php
    $s = $sitio;
    [$etiqueta, $color, $icono] = \App\Models\Sitio::TIPOS[$s->tipo] ?? ['Sitio', '#56696D', 'map-pin'];
    $foto = $s->relationLoaded('fotos') ? $s->fotos->first() : null;
    $datos = array_filter([
        $s->sin_suplemento === null ? null : ['banknotes', $s->sin_suplemento ? 'Sin suplemento' : 'Con suplemento'],
        $s->admite_grandes === null ? null : ['scale', $s->admite_grandes ? 'Perros grandes' : 'Solo pequeños o medianos'],
        $s->puede_entrar === null ? null : ['home', $s->puede_entrar ? 'Puede entrar dentro' : 'Solo terraza'],
        $s->sin_correa === null ? null : ['bolt', $s->sin_correa ? 'Zona sin correa' : 'Con correa'],
    ]);
    $texto = $s->esServicio() ? $s->temporada : ($s->zonas_permitidas ?: $s->otras_condiciones);
@endphp
<article class="group relative flex h-full flex-col overflow-hidden rounded-2xl border border-linea bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md {{ $ancho ? 'w-72 flex-none snap-start' : '' }}">
    @php
        $sellos = (! $s->esServicio() || $s->admite_perros === 'no') || $s->verificado;
    @endphp
    @if ($foto)
        {{-- Con foto: la foto arriba y la respuesta encima --}}
        <div class="relative h-32 overflow-hidden bg-cal">
            <img src="{{ $foto->url() }}" alt="" loading="lazy" class="size-full object-cover">
            <span class="absolute inset-x-3 top-3 flex flex-wrap gap-1.5">
                @if (! $s->esServicio() || $s->admite_perros === 'no')
                    <x-admite :valor="$s->admite_perros" class="px-2 py-1 text-sm shadow-sm" />
                @endif
                @if ($s->verificado)
                    <x-confirmado class="px-2 py-1 text-sm shadow-sm" />
                @endif
            </span>
        </div>
    @else
        {{-- Sin foto: una franja fina con el color de su categoría, para reconocerla de un vistazo --}}
        <span class="block h-1.5" style="background-color: {{ $color }}" aria-hidden="true"></span>
    @endif

    <div class="flex flex-1 flex-col gap-2 p-4">
        @if (! $foto && $sellos)
            <span class="flex flex-wrap gap-1.5">
                @if (! $s->esServicio() || $s->admite_perros === 'no')
                    <x-admite :valor="$s->admite_perros" class="px-2 py-1 text-sm" />
                @endif
                @if ($s->verificado)
                    <x-confirmado class="px-2 py-1 text-sm" />
                @endif
            </span>
        @endif
        <h3 class="text-lg font-extrabold leading-snug">
            <a href="{{ route('mockup', 'rediseno-ficha') }}?sitio={{ $s->id }}" class="no-underline after:absolute after:inset-0 after:content-['']">{{ $s->nombre }}</a>
        </h3>
        <p class="flex items-center gap-1.5 text-sm text-gris"><x-icono-tipo :tipo="$s->tipo" class="size-4 flex-none" />{{ $s->subtipo ?: $etiqueta }} · {{ $s->municipio->nombre }}</p>

        @if ($datos)
            <ul class="flex flex-wrap gap-1.5">
                @foreach ($datos as [$ic, $t])
                    <li class="inline-flex items-center gap-1 rounded-md bg-cal px-2 py-1 text-sm font-bold text-tinta">@svg('heroicon-o-'.$ic, 'size-4 text-pino', ['aria-hidden' => 'true']){{ $t }}</li>
                @endforeach
            </ul>
        @elseif ($texto)
            <p class="line-clamp-2 text-sm text-gris">{{ $texto }}</p>
        @endif

        <div class="mt-auto flex items-center justify-between gap-2 pt-2">
            <button type="button" class="btn relative z-10 min-h-10 px-3 py-1 text-sm">@svg('heroicon-o-heart', 'size-5', ['aria-hidden' => 'true'])Quiero ir</button>
            <span class="inline-flex items-center gap-1.5 text-sm font-bold text-pino">@svg('heroicon-o-list-bullet', 'size-4', ['aria-hidden' => 'true'])Ver condiciones</span>
        </div>
    </div>
</article>
