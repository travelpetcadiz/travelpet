{{-- Envoltorio de las páginas legales: título, fecha de actualización y enlaces entre ellas --}}
@props(['titulo', 'actualizado' => '7 de noviembre de 2026'])
@php
    $paginas = [
        'legal.aviso' => 'Aviso legal',
        'legal.privacidad' => 'Privacidad',
        'legal.cookies' => 'Cookies',
        'legal.condiciones' => 'Condiciones de uso',
    ];
@endphp
<div class="mx-auto max-w-3xl px-4 py-8">
    <nav class="mb-6 flex flex-wrap gap-2 text-sm" aria-label="Páginas legales">
        @foreach ($paginas as $ruta => $nombre)
            <a href="{{ route($ruta) }}" wire:navigate @class(['chip', 'border-tinta bg-tinta text-white' => request()->routeIs($ruta)]) @if (request()->routeIs($ruta)) aria-current="page" @endif>{{ $nombre }}</a>
        @endforeach
    </nav>
    <h1 class="text-3xl font-extrabold">{{ $titulo }}</h1>
    <p class="mt-1 text-sm text-gris">Última actualización: {{ $actualizado }}</p>
    <div class="prosa legal mt-6">
        {{ $slot }}
    </div>
</div>
