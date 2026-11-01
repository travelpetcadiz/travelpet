@props(['valor'])
@php
    [$clase, $texto] = match ($valor) {
        'si' => ['bg-ok-suave text-ok', 'Admite perros'],
        'condiciones' => ['bg-aviso-suave text-aviso', 'Con condiciones'],
        'no' => ['bg-mal-suave text-mal', 'No admite perros'],
        default => ['bg-cal text-gris', 'Sin confirmar'],
    };
@endphp
<span {{ $attributes->class(['sello', $clase]) }}>{{ $texto }}</span>
