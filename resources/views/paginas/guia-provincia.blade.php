@php
    $titulo = 'Sitios que admiten mascotas en la provincia de '.$provincia->nombre;
    $jsonLd = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Inicio', 'item' => route('inicio')],
            ['@type' => 'ListItem', 'position' => 2, 'name' => $provincia->nombre, 'item' => route('guia', $provincia)],
        ],
    ];
@endphp
@component('layouts.app', [
    'title' => $titulo,
    'descripcion' => $total.' sitios en la provincia de '.$provincia->nombre.' que admiten mascotas: alojamientos, restaurantes, playas, parques y veterinarios, pueblo a pueblo y con sus condiciones.',
    'jsonLd' => $jsonLd,
])
    <div class="mx-auto grid max-w-6xl gap-10 px-4 py-6 sm:py-8">
        <header class="grid gap-3">
            <nav aria-label="Estás en">
                <ol class="flex flex-wrap items-center gap-2 text-sm text-gris">
                    <li><a href="{{ route('inicio') }}" wire:navigate class="underline">Inicio</a></li>
                </ol>
            </nav>
            <h1 class="text-3xl font-extrabold leading-tight sm:text-4xl">{{ $titulo }}</h1>
            <p class="max-w-3xl text-lg text-gris">{{ $total }} sitios en la guía, con sus condiciones y de dónde sale cada dato. Elige qué buscas o tu pueblo.</p>
        </header>

        <section aria-labelledby="categorias">
            <h2 id="categorias" class="mb-3 text-2xl font-bold">Qué buscas</h2>
            <ul class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($categorias as $c)
                    <li>
                        <a href="{{ $c['url'] }}" wire:navigate class="tarjeta flex min-h-16 items-center gap-3 p-4 no-underline transition hover:border-gris hover:shadow-sm">
                            <x-icono-tipo :tipo="$c['tipo']" class="size-6" />
                            <span class="flex-1 text-lg font-semibold">{{ $c['texto'] }}</span>
                            <span class="text-gris">{{ $c['total'] }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </section>

        <section aria-labelledby="pueblos">
            <h2 id="pueblos" class="mb-3 text-2xl font-bold">Pueblo a pueblo</h2>
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($comarcas as $comarca => $pueblos)
                    <div>
                        <h3 class="mb-2 text-lg font-bold">{{ $comarca }}</h3>
                        <ul class="grid gap-1">
                            @foreach ($pueblos as $m)
                                <li>
                                    <a href="{{ route('guia.seccion', [$provincia, $m->slug]) }}" wire:navigate class="flex min-h-11 items-center justify-between gap-3 rounded-lg px-3 py-2 font-bold text-pino hover:bg-white">
                                        <span class="underline">{{ $m->nombre }}</span>
                                        <span class="font-normal text-gris">{{ $m->total }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
@endcomponent
