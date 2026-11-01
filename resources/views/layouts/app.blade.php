@php
    $marca = config('app.name');
    $titulo = isset($title) ? $title.' · '.$marca : $marca.' · Guía para viajar con perro';
    $descripcion ??= 'Guía gratuita de sitios pet-friendly: alojamientos, restaurantes, playas, parques y servicios que admiten perros, con sus condiciones y de dónde sale cada dato.';
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
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $marca }}">
    <meta property="og:locale" content="es_ES">
    <meta property="og:title" content="{{ $titulo }}">
    <meta property="og:description" content="{{ $descripcion }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset('hero.jpg') }}">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="icon" href="{{ asset('logo.png') }}">
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
                <span aria-hidden="true" class="grid size-8 place-items-center rounded-full bg-pino text-white">
                    <svg viewBox="0 0 24 24" fill="currentColor" class="size-5"><ellipse cx="6" cy="9.5" rx="2" ry="2.6"/><ellipse cx="10" cy="5.8" rx="2" ry="2.6"/><ellipse cx="14" cy="5.8" rx="2" ry="2.6"/><ellipse cx="18" cy="9.5" rx="2" ry="2.6"/><path d="M12 11c-2.6 0-5.5 3.6-5.5 6.2 0 1.6 1.2 2.5 2.8 2.5 1.1 0 1.8-.6 2.7-.6s1.6.6 2.7.6c1.6 0 2.8-.9 2.8-2.5C17.5 14.6 14.6 11 12 11z"/></svg>
                </span>
                {{ $marca }}
            </a>
            <span class="flex-1"></span>

            <nav class="hidden items-center gap-1 md:flex" aria-label="Principal">
                <a href="{{ route('inicio') }}" wire:navigate class="rounded-lg px-3 py-2 font-bold hover:bg-cal">Explorar</a>
                <a href="{{ route('sugerir') }}" wire:navigate class="rounded-lg px-3 py-2 font-bold hover:bg-cal">Sugerir un sitio</a>
                @auth
                    <a href="{{ route('viaje') }}" wire:navigate class="rounded-lg px-3 py-2 font-bold hover:bg-cal">Mi viaje</a>
                    @if (auth()->user()->isStaff())
                        <a href="{{ url('/admin') }}" class="rounded-lg px-3 py-2 font-bold text-pino hover:bg-cal">Panel</a>
                    @endif
                    <form method="POST" action="{{ route('salir') }}">
                        @csrf
                        <button class="btn ml-1">Salir</button>
                    </form>
                @else
                    <a href="{{ route('entrar') }}" wire:navigate class="rounded-lg px-3 py-2 font-bold hover:bg-cal">Entrar</a>
                    <a href="{{ route('registro') }}" wire:navigate class="btn btn-pri ml-1">Crear cuenta</a>
                @endauth
            </nav>

            <button type="button" class="btn md:hidden" @click="abierto = !abierto" :aria-expanded="abierto" aria-controls="menu-movil">Menú</button>
        </div>

        <nav id="menu-movil" x-show="abierto" x-cloak @click.outside="abierto = false" class="grid gap-1 border-t border-linea px-4 py-3 md:hidden" aria-label="Principal">
            <a href="{{ route('inicio') }}" wire:navigate class="rounded-lg px-3 py-2 font-bold hover:bg-cal">Explorar</a>
            <a href="{{ route('sugerir') }}" wire:navigate class="rounded-lg px-3 py-2 font-bold hover:bg-cal">Sugerir un sitio</a>
            @auth
                <a href="{{ route('viaje') }}" wire:navigate class="rounded-lg px-3 py-2 font-bold hover:bg-cal">Mi viaje</a>
                @if (auth()->user()->isStaff())
                    <a href="{{ url('/admin') }}" class="rounded-lg px-3 py-2 font-bold text-pino hover:bg-cal">Panel de administración</a>
                @endif
                <form method="POST" action="{{ route('salir') }}">
                    @csrf
                    <button class="w-full rounded-lg px-3 py-2 text-left font-bold hover:bg-cal">Salir ({{ auth()->user()->name }})</button>
                </form>
            @else
                <a href="{{ route('entrar') }}" wire:navigate class="rounded-lg px-3 py-2 font-bold hover:bg-cal">Entrar</a>
                <a href="{{ route('registro') }}" wire:navigate class="btn btn-pri">Crear cuenta</a>
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
            <span>Mapa: © OpenStreetMap</span>
        </div>
    </footer>

    @livewireScriptConfig
</body>
</html>
