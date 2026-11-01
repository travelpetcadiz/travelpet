@php
    $marca = config('app.name');
    $titulo = isset($title) ? $title.' · '.$marca : $marca.' · Guía para viajar con mascota';
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
    <meta property="og:image" content="{{ asset('hero.jpg') }}">
    <meta name="twitter:card" content="summary_large_image">
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

    <header x-data="{ abierto: false }" class="sticky top-0 z-30 border-b border-linea bg-white/95 backdrop-blur">
        <div class="mx-auto flex max-w-6xl items-center gap-3 px-4 py-2.5">
            <a href="{{ route('inicio') }}" wire:navigate class="flex items-center gap-2 font-display text-xl font-extrabold tracking-tight text-pino no-underline">
                <img src="{{ asset('logo.png') }}" alt="" width="36" height="36" class="size-9">
                {{ $marca }}
            </a>
            <span class="flex-1"></span>

            @php
                $ic = fn (string $n) => svg('heroicon-o-'.$n, 'size-5 flex-none', ['aria-hidden' => 'true'])->toHtml();
                $enlace = 'inline-flex items-center gap-2 rounded-lg px-3 py-2 font-bold hover:bg-cal';
            @endphp
            <nav class="hidden items-center gap-1 lg:flex" aria-label="Principal">
                <a href="{{ route('inicio') }}" wire:navigate class="{{ $enlace }}">{!! $ic('magnifying-glass') !!}Explorar</a>
                <a href="{{ route('como-funciona') }}" wire:navigate class="{{ $enlace }}">{!! $ic('question-mark-circle') !!}Cómo funciona</a>
                <a href="{{ route('sugerir') }}" wire:navigate class="{{ $enlace }}">{!! $ic('light-bulb') !!}Sugerir un sitio</a>
                @auth
                    <a href="{{ route('viaje') }}" wire:navigate class="{{ $enlace }}">{!! $ic('bookmark') !!}Mi viaje</a>
                    @if (auth()->user()->isStaff())
                        <a href="{{ url('/admin') }}" class="{{ $enlace }} text-pino">{!! $ic('cog-6-tooth') !!}Panel</a>
                    @endif
                    <form method="POST" action="{{ route('salir') }}">
                        @csrf
                        <button class="btn ml-1">{!! $ic('arrow-left-start-on-rectangle') !!}Salir</button>
                    </form>
                @else
                    <a href="{{ route('entrar') }}" wire:navigate class="{{ $enlace }}">{!! $ic('arrow-right-end-on-rectangle') !!}Entrar</a>
                    <a href="{{ route('registro') }}" wire:navigate class="btn btn-pri ml-1">{!! $ic('user-plus') !!}Crear cuenta</a>
                @endauth
            </nav>

            <button type="button" class="btn lg:hidden" @click="abierto = !abierto" :aria-expanded="abierto" aria-controls="menu-movil">{!! $ic('bars-3') !!}Menú</button>
        </div>

        <nav id="menu-movil" x-show="abierto" x-cloak @click.outside="abierto = false" class="grid gap-1 border-t border-linea px-4 py-3 lg:hidden" aria-label="Principal">
            <a href="{{ route('inicio') }}" wire:navigate class="{{ $enlace }}">{!! $ic('magnifying-glass') !!}Explorar</a>
            <a href="{{ route('como-funciona') }}" wire:navigate class="{{ $enlace }}">{!! $ic('question-mark-circle') !!}Cómo funciona</a>
            <a href="{{ route('urgencias') }}" wire:navigate class="{{ $enlace }} text-mal">{!! $ic('lifebuoy') !!}Urgencias veterinarias 24 h</a>
            <a href="{{ route('sugerir') }}" wire:navigate class="{{ $enlace }}">{!! $ic('light-bulb') !!}Sugerir un sitio</a>
            @auth
                <a href="{{ route('viaje') }}" wire:navigate class="{{ $enlace }}">{!! $ic('bookmark') !!}Mi viaje</a>
                @if (auth()->user()->isStaff())
                    <a href="{{ url('/admin') }}" class="{{ $enlace }} text-pino">{!! $ic('cog-6-tooth') !!}Panel de administración</a>
                @endif
                <form method="POST" action="{{ route('salir') }}">
                    @csrf
                    <button class="{{ $enlace }} w-full text-left">{!! $ic('arrow-left-start-on-rectangle') !!}Salir ({{ auth()->user()->name }})</button>
                </form>
            @else
                <a href="{{ route('entrar') }}" wire:navigate class="{{ $enlace }}">{!! $ic('arrow-right-end-on-rectangle') !!}Entrar</a>
                <a href="{{ route('registro') }}" wire:navigate class="btn btn-pri">{!! $ic('user-plus') !!}Crear cuenta</a>
            @endauth
        </nav>
    </header>

    <main id="contenido" class="flex-1">
        {{ $slot }}
    </main>

    <footer class="border-t border-linea bg-white">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center gap-x-5 gap-y-2 px-4 py-5 text-sm text-gris">
            <span>© {{ date('Y') }} {{ $marca }} · Guía gratuita</span>
            <a href="{{ route('legal.aviso') }}" class="underline">Aviso legal</a>
            <a href="{{ route('legal.privacidad') }}" class="underline">Privacidad</a>
            <a href="{{ route('como-funciona') }}" class="underline">Cómo funciona</a>
            <a href="{{ route('urgencias') }}" class="underline">Urgencias veterinarias 24 h</a>
            <span>Mapa: © OpenStreetMap</span>
        </div>
    </footer>

    @livewireScriptConfig
</body>
</html>
