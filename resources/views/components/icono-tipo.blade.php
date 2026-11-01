{{-- Icono de la categoría de un sitio, con su color --}}
@props(['tipo'])
@php([$etiqueta, $color, $icono] = \App\Models\Sitio::TIPOS[$tipo] ?? [$tipo, '#56696D', 'map-pin'])
@svg('heroicon-s-'.$icono, $attributes->get('class', 'size-4').' flex-none', ['style' => 'color: '.$color, 'aria-hidden' => 'true'])
