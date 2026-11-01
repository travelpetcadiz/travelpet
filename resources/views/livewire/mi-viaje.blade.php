<div class="mx-auto max-w-4xl px-4 py-8">
    <h1 class="text-3xl font-extrabold">Mi viaje</h1>
    <p class="mt-1 text-gris">Los sitios que has guardado. Se guardan en tu cuenta, así que los verás en cualquier dispositivo.</p>

    @foreach (['Quiero ir' => $quiero, 'He ido' => $visitados] as $titulo => $lista)
        <section class="mt-8">
            <h2 class="mb-3 text-xl font-bold">{{ $titulo }} <span class="font-normal text-gris">({{ $lista->count() }})</span></h2>
            @forelse ($lista as $s)
                <div wire:key="v-{{ $s->id }}" class="tarjeta mb-2 flex items-center gap-3 p-3">
                    <span class="size-2.5 flex-none rounded-full" style="background: {{ $s->color() }}"></span>
                    <a href="{{ route('sitio', $s) }}" wire:navigate class="min-w-0 flex-1 no-underline">
                        <strong class="block truncate">{{ $s->nombre }}</strong>
                        <span class="text-sm text-gris">{{ $s->etiquetaTipo() }} · {{ $s->municipio->nombre }}</span>
                    </a>
                    <button type="button" wire:click="quitar({{ $s->id }})" class="btn" aria-label="Quitar {{ $s->nombre }}">Quitar</button>
                </div>
            @empty
                <p class="text-gris">Nada por aquí todavía. <a href="{{ route('inicio') }}" wire:navigate class="font-bold text-pino underline">Explora sitios</a> y guárdalos desde su ficha.</p>
            @endforelse
        </section>
    @endforeach
</div>
