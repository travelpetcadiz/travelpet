@php
    $ic = fn (string $n, string $c = 'size-5') => svg('heroicon-o-'.$n, $c.' flex-none', ['aria-hidden' => 'true'])->toHtml();
    $plural = fn (int $n, string $uno, string $varios) => $n.' '.($n === 1 ? $uno : $varios);

    // Resumen: cuántos hay y qué sabemos de ellos («12 admiten mascotas, 4 con condiciones y 1 no las admite»)
    $partes = [];
    if ($admite) {
        $dicho = false;
        foreach (['si', 'condiciones', 'no_consta', 'no'] as $valor) {
            if (! $n = $admite[$valor] ?? 0) {
                continue;
            }
            $v = $n === 1 ? 'admite' : 'admiten';
            $partes[] = $n.' '.match ($valor) {
                'si' => "{$v} mascotas",
                'condiciones' => $dicho ? 'con condiciones' : "{$v} mascotas con condiciones",
                'no_consta' => 'aún sin confirmar',
                'no' => $dicho ? "no las {$v}" : "no {$v} mascotas",
            };
            $dicho = $dicho || $valor !== 'no_consta';
        }
    }
    $resumen = $plural($total, 'sitio', $nombre).' en la guía'.($partes ? ': '.\Illuminate\Support\Arr::join($partes, ', ', ' y ') : '');
    $descripcion = $resumen.'. Con sus condiciones y de dónde sale cada dato.';
@endphp
@component('layouts.app', ['title' => $h1, 'descripcion' => $descripcion, 'noindex' => $noindex, 'jsonLd' => $jsonLd])
    <div class="mx-auto grid max-w-6xl gap-8 px-4 py-6 sm:py-8">
        <header class="grid gap-3">
            <nav aria-label="Estás en">
                <ol class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-gris">
                    <li><a href="{{ route('inicio') }}" wire:navigate class="underline">Inicio</a></li>
                    @foreach ($migas as [$texto, $url])
                        <li class="flex items-center gap-2">@svg('heroicon-m-chevron-right', 'size-4 flex-none', ['aria-hidden' => 'true'])<a href="{{ $url }}" wire:navigate class="underline">{{ $texto }}</a></li>
                    @endforeach
                </ol>
            </nav>
            <h1 class="text-3xl font-extrabold leading-tight sm:text-4xl">{{ $h1 }}</h1>
            <p class="max-w-3xl text-lg text-gris">
                Hay {{ $resumen }}.
                @if ($confirmados)
                    {{ $confirmados === 1 ? '1 está confirmado' : $confirmados.' están confirmados' }} por el propio sitio.
                @endif
                Lo que no sabemos te lo decimos: confírmalo antes de ir.
            </p>
            @if ($conMapa)
                <div><a href="{{ $mapa }}" wire:navigate class="btn btn-pri">{!! $ic('map') !!}Ver en el mapa</a></div>
            @endif
        </header>

        @foreach ($grupos as $grupo)
            <section @if ($grupo['titulo']) aria-labelledby="g-{{ $loop->index }}" @endif>
                @if ($grupo['titulo'])
                    <div class="mb-3 flex flex-wrap items-baseline justify-between gap-x-4 gap-y-1">
                        <h2 id="g-{{ $loop->index }}" class="text-2xl font-bold">{{ $grupo['titulo'] }} <span class="text-lg font-normal text-gris">({{ $grupo['sitios']->count() }})</span></h2>
                        @if ($grupo['enlace'] && count($grupos) > 1)
                            <a href="{{ $grupo['enlace'] }}" wire:navigate class="font-bold text-pino underline">{{ $grupo['enlaceTexto'] }}</a>
                        @endif
                    </div>
                @endif
                <ul class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($grupo['sitios'] as $s)
                        <li class="flex flex-col">
                            <x-tarjeta-sitio :sitio="$s" :nivel="$grupo['titulo'] ? 'h3' : 'h2'" />
                        </li>
                    @endforeach
                </ul>
            </section>
        @endforeach

        @if ($otras)
            <section aria-labelledby="otras">
                <h2 id="otras" class="mb-3 text-2xl font-bold">{{ $otrasTitulo }}</h2>
                <ul class="flex flex-wrap gap-2">
                    @foreach ($otras as $o)
                        <li><a href="{{ $o['url'] }}" wire:navigate class="chip no-underline"><x-icono-tipo :tipo="$o['tipo']" />{{ $o['texto'] }} <span class="font-normal text-gris">({{ $o['total'] }})</span></a></li>
                    @endforeach
                </ul>
            </section>
        @endif

        <section class="tarjeta flex flex-wrap items-center justify-between gap-4 p-5">
            <div>
                <h2 class="text-xl font-bold">¿Falta algún sitio o ha cambiado algo?</h2>
                <p class="mt-1 text-gris">Cuéntanoslo y lo comprobamos.</p>
            </div>
            <a href="{{ route('sugerir') }}" wire:navigate class="btn">{!! $ic('light-bulb') !!}Sugerir un sitio</a>
        </section>
    </div>
@endcomponent
