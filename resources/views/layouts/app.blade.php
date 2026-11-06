@php
    $marca = config('app.name');
    $titulo = isset($title) ? $title.' · '.$marca : $marca.' · Guía de sitios que admiten mascotas en Cádiz';
    $descripcion ??= 'Guía gratuita de sitios pet-friendly: alojamientos, restaurantes, playas, parques y servicios que admiten mascotas, con sus condiciones y de dónde sale cada dato.';
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#0E7F8A">
    <title>{{ $titulo }}</title>
    <meta name="description" content="{{ $descripcion }}">
    <link rel="canonical" href="{{ url()->current() }}">
    @if ($noindex ?? false)
        <meta name="robots" content="noindex, nofollow">
    @endif
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $marca }}">
    <meta property="og:locale" content="es_ES">
    <meta property="og:title" content="{{ $titulo }}">
    <meta property="og:description" content="{{ $descripcion }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset('compartir.jpg') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="TravelPet Cádiz: ¿Puedo ir con mi mascota? Guía gratuita de la provincia de Cádiz">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <link rel="icon" href="{{ asset('logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    @isset($jsonLd)
        <script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
    @endisset
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="flex min-h-dvh flex-col">
    <a href="#contenido" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 btn">Saltar al contenido</a>

    @php
        $ic = fn (string $n) => svg('heroicon-o-'.$n, 'size-5 flex-none', ['aria-hidden' => 'true'])->toHtml();
        $enlace = 'inline-flex items-center gap-2 whitespace-nowrap rounded-lg px-3 py-2 font-bold hover:bg-cal';
        $enlaceMenu = 'flex min-h-11 items-center gap-3 rounded-lg px-2 py-2 font-bold hover:bg-cal';
        $actual = fn (string $ruta) => request()->routeIs($ruta) ? 'bg-pino-suave text-pino' : '';
        // Categorías: lo que se busca para salir con la mascota y lo que se busca para la mascota
        $salir = ['Alojamiento', 'Restauración', 'Playa', 'Espacio', 'Transporte y ocio'];
        $paraMascota = \App\Models\Sitio::SERVICIOS;
        $chipCategoria = fn (string $clave, string $extra = '') => '<a href="'.e(route('inicio', ['tipo' => $clave])).'" wire:navigate class="chip '.$extra.'">'
            .svg('heroicon-s-'.\App\Models\Sitio::TIPOS[$clave][2], 'size-5 flex-none', ['style' => 'color: '.\App\Models\Sitio::TIPOS[$clave][1], 'aria-hidden' => 'true'])->toHtml()
            .e(\App\Models\Sitio::TIPOS[$clave][0]).'</a>';
        $globo = '<span x-show="$store.viaje.total" x-cloak x-text="$store.viaje.total" class="absolute -right-1.5 -top-1.5 grid size-5 place-items-center rounded-full bg-albero text-xs font-bold text-tinta"></span>';
    @endphp
    <header x-data="{ abierto: false }" @keydown.escape.window="abierto = false" class="sticky top-0 z-30 border-b border-linea bg-white/95 backdrop-blur">
        <div class="mx-auto flex max-w-6xl items-center gap-2 px-4 py-2.5">
            <a href="{{ route('inicio') }}" wire:navigate class="flex flex-none items-center gap-1.5 font-display text-lg font-extrabold tracking-tight text-pino no-underline sm:gap-2 sm:text-xl">
                <img src="{{ asset('logo.png') }}" alt="" width="36" height="36" class="size-8 sm:size-9">
                {{ $marca }}
            </a>
            <span class="flex-1"></span>

            <nav class="hidden items-center gap-1 xl:flex" aria-label="Principal">
                <a href="{{ route('inicio') }}" wire:navigate class="{{ $enlace }}">{!! $ic('magnifying-glass') !!}Explorar</a>
                <a href="{{ route('como-funciona') }}" wire:navigate class="{{ $enlace }}">{!! $ic('question-mark-circle') !!}Cómo funciona</a>
                <a href="{{ route('transporte') }}" wire:navigate class="{{ $enlace }} {{ $actual('transporte') }}" @if (request()->routeIs('transporte')) aria-current="page" @endif>{!! $ic('truck') !!}<span class="2xl:hidden">Transporte</span><span class="hidden 2xl:inline">Transporte con mascota</span></a>
                <a href="{{ route('sugerir') }}" wire:navigate class="{{ $enlace }}">{!! $ic('light-bulb') !!}Sugerir un sitio</a>
                @auth
                    @if (auth()->user()->isStaff())
                        <a href="{{ url('/admin') }}" class="{{ $enlace }} text-pino">{!! $ic('cog-6-tooth') !!}Panel</a>
                    @endif
                    <form method="POST" action="{{ route('salir') }}">
                        @csrf
                        <button class="btn ml-1">{!! $ic('arrow-left-start-on-rectangle') !!}Salir</button>
                    </form>
                @else
                    <a href="{{ route('entrar') }}" wire:navigate class="{{ $enlace }}">{!! $ic('arrow-right-end-on-rectangle') !!}Entrar</a>
                    <a href="{{ route('registro') }}" wire:navigate class="btn btn-pri ml-1 whitespace-nowrap">{!! $ic('user-plus') !!}Crear cuenta</a>
                @endauth
            </nav>

            {{-- Mi viaje siempre a la vista, también sin cuenta: así se ve que se puede planificar el viaje entero --}}
            <a href="{{ route('viaje') }}" wire:navigate class="btn relative min-h-11 gap-1.5 whitespace-nowrap border-pino bg-pino-suave px-2.5 text-sm text-pino sm:px-4 sm:text-base xl:ml-1" @if (request()->routeIs('viaje')) aria-current="page" @endif>{!! svg('heroicon-o-bookmark', 'size-5 flex-none', ['aria-hidden' => 'true'])->toHtml() !!}Mi viaje{!! $globo !!}</a>
            <button type="button" class="btn min-h-11 gap-1.5 whitespace-nowrap px-2.5 text-sm sm:px-4 sm:text-base xl:hidden" @click="abierto = !abierto" :aria-expanded="abierto" aria-controls="menu-movil">{!! $ic('bars-3') !!}Menú</button>
        </div>

        {{-- Menú del móvil, en bloques --}}
        <nav id="menu-movil" x-show="abierto" x-cloak @click.outside="abierto = false" class="max-h-[calc(100dvh-60px)] divide-y divide-linea overflow-y-auto border-t border-linea px-4 pb-4 xl:hidden" aria-label="Principal">
            <div class="pb-3">
                <p class="px-2 pb-2 pt-3 text-sm font-bold text-gris">Salir con tu mascota</p>
                <div class="flex flex-wrap gap-2">
                    @foreach ($salir as $clave)
                        {!! $chipCategoria($clave, 'min-h-11 text-base') !!}
                    @endforeach
                </div>
            </div>
            <div class="pb-3">
                <p class="px-2 pb-2 pt-3 text-sm font-bold text-gris">Para tu mascota</p>
                <div class="flex flex-wrap gap-2">
                    @foreach ($paraMascota as $clave)
                        {!! $chipCategoria($clave, 'min-h-11 text-base') !!}
                    @endforeach
                    <a href="{{ route('urgencias') }}" wire:navigate class="chip min-h-11 border-mal/30 text-base text-mal">{!! $ic('lifebuoy') !!}Urgencias veterinarias 24 h</a>
                </div>
            </div>
            <div class="pb-2">
                <p class="px-2 pb-1 pt-3 text-sm font-bold text-gris">Planifica</p>
                <a href="{{ route('transporte') }}" wire:navigate class="{{ $enlaceMenu }} {{ $actual('transporte') }}">{!! $ic('truck') !!}Transporte con mascota</a>
                @foreach (\App\Models\Provincia::activas()->has('sitios')->orderBy('nombre')->get(['nombre', 'slug']) as $p)
                    <a href="{{ route('guia', $p) }}" class="{{ $enlaceMenu }}">{!! $ic('map-pin') !!}Pueblo a pueblo{{ $loop->count > 1 ? ' ('.$p->nombre.')' : '' }}</a>
                @endforeach
                <a href="{{ route('como-funciona') }}" wire:navigate class="{{ $enlaceMenu }}">{!! $ic('question-mark-circle') !!}Cómo funciona</a>
            </div>
            <div class="pb-2">
                <p class="px-2 pb-1 pt-3 text-sm font-bold text-gris">Lo tuyo</p>
                <a href="{{ route('viaje') }}" wire:navigate class="{{ $enlaceMenu }}">{!! $ic('bookmark') !!}Mi viaje
                    <span x-show="$store.viaje.total" x-cloak class="ml-auto rounded-full bg-albero-suave px-2 text-sm text-aviso"><span x-text="$store.viaje.total"></span> <span x-text="$store.viaje.total === 1 ? 'guardado' : 'guardados'"></span></span></a>
                <a href="{{ route('sugerir') }}" wire:navigate class="{{ $enlaceMenu }}">{!! $ic('light-bulb') !!}Sugerir un sitio</a>
                <a href="{{ route('instalar') }}" wire:navigate class="{{ $enlaceMenu }}">{!! $ic('device-phone-mobile') !!}Añade TravelPet a tu móvil</a>
                @auth
                    @if (auth()->user()->isStaff())
                        <a href="{{ url('/admin') }}" class="{{ $enlaceMenu }} text-pino">{!! $ic('cog-6-tooth') !!}Panel de administración</a>
                    @endif
                @endauth
            </div>
            <div class="pt-3">
                <a href="{{ route('negocios') }}" wire:navigate class="flex items-center gap-3 rounded-xl bg-albero-suave p-3 no-underline">
                    {!! svg('heroicon-o-briefcase', 'size-6 flex-none text-aviso', ['aria-hidden' => 'true'])->toHtml() !!}
                    <span><strong class="block">¿Tienes un negocio que admite mascotas?</strong><span class="text-sm text-gris">Aparecer en la guía es gratis</span></span>
                </a>
                @auth
                    <form method="POST" action="{{ route('salir') }}" class="mt-3">
                        @csrf
                        <button class="btn w-full">{!! $ic('arrow-left-start-on-rectangle') !!}Salir ({{ auth()->user()->name }})</button>
                    </form>
                @else
                    <div class="mt-3 grid grid-cols-2 gap-2">
                        <a href="{{ route('entrar') }}" wire:navigate class="btn">{!! $ic('arrow-right-end-on-rectangle') !!}Entrar</a>
                        <a href="{{ route('registro') }}" wire:navigate class="btn btn-pri">{!! $ic('user-plus') !!}Crear cuenta</a>
                    </div>
                @endauth
            </div>
        </nav>
    </header>

    {{-- Categorías siempre a mano. En Explorar no hace falta: allí ya están los accesos y los filtros --}}
    @unless (request()->routeIs('inicio'))
        <nav class="border-b border-linea bg-white" aria-label="Categorías">
            <div class="mx-auto flex max-w-6xl gap-2 overflow-x-auto px-4 py-2.5 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                @foreach ([...$salir, ...$paraMascota] as $clave)
                    {!! $chipCategoria($clave) !!}
                @endforeach
                <a href="{{ route('urgencias') }}" wire:navigate class="chip border-mal/30 text-mal">{!! svg('heroicon-o-lifebuoy', 'size-5 flex-none', ['aria-hidden' => 'true'])->toHtml() !!}Urgencias 24 h</a>
            </div>
        </nav>
    @endunless

    <main id="contenido" class="flex-1">
        {{ $slot }}
    </main>

    <footer class="border-t border-linea bg-white">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center gap-x-5 gap-y-2 px-4 py-5 text-sm text-gris">
            <span>© {{ date('Y') }} {{ $marca }} · Guía gratuita</span>
            <a href="{{ route('legal.aviso') }}" class="underline">Aviso legal</a>
            <a href="{{ route('legal.privacidad') }}" class="underline">Privacidad</a>
            <a href="{{ route('como-funciona') }}" class="underline">Cómo funciona</a>
            {{-- Para Google y para quien quiere ver la guía por pueblos --}}
            @foreach ($provinciasPie = \App\Models\Provincia::activas()->has('sitios')->orderBy('nombre')->get(['nombre', 'slug']) as $p)
                <a href="{{ route('guia', $p) }}" class="underline">Pueblo a pueblo{{ $provinciasPie->count() > 1 ? ' ('.$p->nombre.')' : '' }}</a>
            @endforeach
            <a href="{{ route('transporte') }}" class="underline">Transporte con mascota</a>
            <a href="{{ route('urgencias') }}" class="underline">Urgencias veterinarias 24 h</a>
            <a href="{{ route('negocios') }}" class="underline">¿Tienes un negocio?</a>
            <a href="{{ route('instalar') }}" class="underline">Añade TravelPet a tu móvil</a>
            <span>Mapa: © OpenStreetMap</span>
        </div>
    </footer>

    {{-- Mi viaje (resources/js/viaje.js): lo guardado en la cuenta, si se ha entrado --}}
    <script type="application/json" id="tp-viaje">@json(auth()->check() ? ['cuenta' => true, 'url' => route('viaje.guardar'), 'items' => (object) auth()->user()->viajeItems()] : ['cuenta' => false])</script>
    <div x-data x-show="$store.viaje.aviso" x-cloak x-transition.opacity role="status" aria-live="polite"
         class="fixed inset-x-4 bottom-4 z-40 mx-auto flex max-w-md items-center gap-3 rounded-xl bg-tinta px-4 py-3 text-white shadow-lg">
        {!! svg('heroicon-s-bookmark', 'size-5 flex-none text-albero', ['aria-hidden' => 'true'])->toHtml() !!}
        <span class="flex-1" x-text="$store.viaje.aviso"></span>
        <a href="{{ route('viaje') }}" wire:navigate class="font-bold text-albero underline">Ver</a>
    </div>

    @livewireScriptConfig
</body>
</html>
