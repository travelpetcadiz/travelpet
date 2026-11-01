@props(['valor'])
@php
    [$clase, $icono, $texto] = match ($valor) {
        'si' => ['bg-ok-suave text-ok', 'check-circle', 'Admite perros'],
        'condiciones' => ['bg-aviso-suave text-aviso', 'exclamation-triangle', 'Admite, con condiciones'],
        'no' => ['bg-mal-suave text-mal', 'no-symbol', 'No admite perros'],
        default => ['bg-cal text-gris', 'question-mark-circle', 'Sin confirmar'],
    };
@endphp
<span {{ $attributes->class(['sello', $clase]) }}>@svg('heroicon-s-'.$icono, 'size-4 flex-none', ['aria-hidden' => 'true']){{ $texto }}</span>
