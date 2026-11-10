{{-- Barra de los bocetos: recuerda que no es la web real y deja saltar entre pantallas --}}
@php
    $pantallasBoceto = [
        'portada' => ['home', 'Portada'],
        'resultados' => ['list-bullet', 'Resultados'],
        'ficha' => ['document-text', 'Ficha'],
        'rediseno-portada' => ['sparkles', 'Rediseño: portada'],
        'rediseno-lista' => ['sparkles', 'Rediseño: lista'],
        'rediseno-ficha' => ['sparkles', 'Rediseño: ficha'],
    ];
@endphp
<div class="border-b border-albero bg-albero-suave text-sm">
    <div class="mx-auto flex max-w-6xl flex-wrap items-center gap-x-4 gap-y-1 px-4 py-2">
        <span class="inline-flex items-center gap-1.5 font-bold text-aviso">@svg('heroicon-s-eye', 'size-4', ['aria-hidden' => 'true'])Boceto: la web real no cambia</span>
        <nav class="flex flex-wrap gap-x-3" aria-label="Bocetos">
            <a href="{{ route('mockup') }}" class="inline-flex min-h-9 items-center gap-1 underline">@svg('heroicon-o-squares-2x2', 'size-4', ['aria-hidden' => 'true'])Todos</a>
            @foreach ($pantallasBoceto as $clave => [$icono, $texto])
                <a href="{{ route('mockup', $clave) }}" @class(['inline-flex min-h-9 items-center gap-1', 'font-bold no-underline' => ($actual ?? '') === $clave, 'underline' => ($actual ?? '') !== $clave])>@svg('heroicon-o-'.$icono, 'size-4', ['aria-hidden' => 'true']){{ $texto }}</a>
            @endforeach
        </nav>
    </div>
</div>
