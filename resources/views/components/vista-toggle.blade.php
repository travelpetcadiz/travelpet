{{-- Cambiar entre lista y mapa (dentro de la vista de Explorar) --}}
@props(['vista'])
<div class="flex rounded-full border border-linea bg-cal p-1" role="group" aria-label="Ver como">
    <button type="button" wire:click="$set('vista', 'lista')" aria-pressed="{{ $vista === 'lista' ? 'true' : 'false' }}"
        class="inline-flex items-center gap-1.5 rounded-full px-3 text-sm font-bold sm:px-4 {{ $vista === 'lista' ? 'bg-pino text-white' : 'text-gris' }}">@svg('heroicon-o-list-bullet', 'size-5', ['aria-hidden' => 'true'])Lista</button>
    <button type="button" wire:click="$set('vista', 'mapa')" aria-pressed="{{ $vista === 'mapa' ? 'true' : 'false' }}"
        class="inline-flex items-center gap-1.5 rounded-full px-3 text-sm font-bold sm:px-4 {{ $vista === 'mapa' ? 'bg-pino text-white' : 'text-gris' }}">@svg('heroicon-o-map', 'size-5', ['aria-hidden' => 'true'])Mapa</button>
</div>
