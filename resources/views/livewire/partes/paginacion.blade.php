{{-- Paginación de TravelPet: flechas y números de página. En el móvil, flechas y «Página 2 de 5» --}}
@if ($paginator->hasPages())
    @php
        $actual = $paginator->currentPage();
        $ultima = $paginator->lastPage();
        $nombre = $paginator->getPageName();
        // Primera, última y las de alrededor de la actual; el resto, «…»
        $paginas = collect(range(1, $ultima))
            ->filter(fn ($n) => $n === 1 || $n === $ultima || abs($n - $actual) <= 1)
            ->values();
        // Al cambiar de página, se vuelve arriba de la lista
        $subir = "document.getElementById('contenido').scrollIntoView()";
        $flecha = 'grid size-11 place-items-center rounded-full border border-linea bg-white text-tinta transition hover:border-pino hover:text-pino';
        $apagada = 'grid size-11 place-items-center rounded-full border border-linea bg-cal text-linea';
    @endphp
    <nav class="mt-6 flex items-center justify-center gap-2" aria-label="Páginas de resultados">
        @if ($paginator->onFirstPage())
            <span class="{{ $apagada }}" aria-hidden="true">@svg('heroicon-o-chevron-left', 'size-5')</span>
        @else
            <button type="button" wire:click="previousPage('{{ $nombre }}')" x-on:click="{{ $subir }}" wire:loading.attr="disabled" class="{{ $flecha }}">
                @svg('heroicon-o-chevron-left', 'size-5', ['aria-hidden' => 'true'])<span class="sr-only">Página anterior</span>
            </button>
        @endif

        <span class="px-2 text-sm font-bold text-gris sm:hidden">Página {{ $actual }} de {{ $ultima }}</span>
        <ul class="hidden items-center gap-1.5 sm:flex">
            @foreach ($paginas as $i => $n)
                @if ($i > 0 && $n - $paginas[$i - 1] > 1)
                    <li class="px-1 text-gris" aria-hidden="true">…</li>
                @endif
                <li wire:key="pagina-{{ $n }}">
                    @if ($n === $actual)
                        <span class="grid size-11 place-items-center rounded-full bg-pino font-bold text-white" aria-current="page"><span class="sr-only">Página </span>{{ $n }}</span>
                    @else
                        <button type="button" wire:click="gotoPage({{ $n }}, '{{ $nombre }}')" x-on:click="{{ $subir }}" class="grid size-11 place-items-center rounded-full font-bold text-tinta hover:bg-pino-suave hover:text-pino"><span class="sr-only">Página </span>{{ $n }}</button>
                    @endif
                </li>
            @endforeach
        </ul>

        @if ($paginator->hasMorePages())
            <button type="button" wire:click="nextPage('{{ $nombre }}')" x-on:click="{{ $subir }}" wire:loading.attr="disabled" class="{{ $flecha }}">
                @svg('heroicon-o-chevron-right', 'size-5', ['aria-hidden' => 'true'])<span class="sr-only">Página siguiente</span>
            </button>
        @else
            <span class="{{ $apagada }}" aria-hidden="true">@svg('heroicon-o-chevron-right', 'size-5')</span>
        @endif
    </nav>
@endif
