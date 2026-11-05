<div class="mx-auto max-w-4xl px-4 py-8"
     x-data
     x-init="
        const enviar = items => $store.viaje.cuenta ? $wire.$refresh() : $wire.cargarLocal(items);
        if (! $store.viaje.cuenta) $wire.cargarLocal($store.viaje.items);
        $watch('$store.viaje.items', enviar);
     ">
    @php
        $vacio = $quiero->isEmpty() && $visitados->isEmpty();
    @endphp
    <h1 class="text-3xl font-extrabold">Mi viaje</h1>

    @if (! $cargado)
        <p class="mt-4 text-gris" role="status">Cargando lo que has guardado…</p>
    @elseif ($vacio)
        {{-- Vacío: se explica para qué sirve, para que se vea que se puede planificar la escapada entera --}}
        <p class="mt-1 max-w-2xl text-lg text-gris">Planifica tu escapada entera con tu mascota: dónde dormir, dónde comer, qué playas y paseos, y el veterinario más cercano por si acaso.</p>
        <ol class="mt-6 grid max-w-2xl gap-3">
            @foreach ([
                'Busca un pueblo o una categoría.',
                'Pulsa <strong class="inline-flex items-center gap-1 align-bottom">'.svg('heroicon-o-heart', 'size-5 text-pino', ['aria-hidden' => 'true'])->toHtml().'Quiero ir</strong> en los sitios que te gusten.',
                'Aquí los tendrás todos juntos y podrás compartirlos con quien viaja contigo.',
            ] as $n => $texto)
                <li class="flex gap-3"><span class="grid size-8 flex-none place-items-center rounded-full bg-pino font-bold text-white">{{ $n + 1 }}</span><span class="pt-1">{!! $texto !!}</span></li>
            @endforeach
        </ol>
        <a href="{{ route('inicio') }}" wire:navigate class="btn btn-pri mt-6 w-full sm:w-auto">@svg('heroicon-o-magnifying-glass', 'size-5', ['aria-hidden' => 'true'])Empezar a buscar</a>
        @guest
            <p class="mt-3 text-sm text-gris">No hace falta cuenta. Sin cuenta, se guardan en este móvil.</p>
        @endguest
    @else
        <p class="mt-1 text-gris">
            @auth
                Los sitios que has guardado. Se guardan en tu cuenta, así que los verás en cualquier dispositivo.
            @else
                Los sitios que has guardado. Sin cuenta, se quedan en este móvil.
            @endauth
        </p>

        @guest
            <div class="mt-4 flex items-start gap-3 rounded-xl bg-pino-suave p-4">
                @svg('heroicon-o-information-circle', 'size-6 flex-none text-pino', ['aria-hidden' => 'true'])
                <div>
                    <p><strong>¿Cambias de móvil o borras el historial?</strong> Se perderían. Con una cuenta los ves en cualquier dispositivo.</p>
                    <a href="{{ route('registro') }}" wire:navigate class="btn btn-pri mt-3">@svg('heroicon-o-user-plus', 'size-5', ['aria-hidden' => 'true'])Crear cuenta gratis</a>
                </div>
            </div>
        @endguest

        @php
            $lineas = fn ($lista) => $lista->map(fn ($s) => '• '.$s->nombre.' ('.$s->municipio->nombre.'): '.route('sitio', $s))->implode("\n");
            $texto = trim(($quiero->isNotEmpty() ? "Quiero ir con mi mascota a:\n".$lineas($quiero)."\n\n" : '').($visitados->isNotEmpty() ? "Ya hemos ido a:\n".$lineas($visitados)."\n\n" : '')).
                "\n\nLo he guardado en TravelPet: ".route('inicio');
        @endphp
        <button type="button" class="btn mt-4"
            x-data="{ texto: @js($texto) }"
            @click="navigator.share
                ? navigator.share({ title: 'Mi viaje con TravelPet', text: texto }).catch(() => {})
                : window.open('https://wa.me/?text=' + encodeURIComponent(texto), '_blank', 'noopener')">
            @svg('heroicon-o-share', 'size-5', ['aria-hidden' => 'true'])Compartir mi viaje
        </button>

        @foreach (['quiero' => ['Quiero ir', $quiero], 'visitado' => ['He ido', $visitados]] as $estado => [$titulo, $lista])
            <section class="mt-8">
                <h2 class="mb-3 text-xl font-bold">{{ $titulo }} <span class="font-normal text-gris">({{ $lista->count() }})</span></h2>
                @forelse ($lista as $s)
                    <div wire:key="v-{{ $s->id }}" class="tarjeta mb-2 flex items-center gap-3 p-3">
                        <span class="size-2.5 flex-none rounded-full" style="background: {{ $s->color() }}"></span>
                        <a href="{{ route('sitio', $s) }}" wire:navigate class="min-w-0 flex-1 no-underline">
                            <strong class="block truncate">{{ $s->nombre }}</strong>
                            <span class="text-sm text-gris">{{ $s->etiquetaTipo() }} · {{ $s->municipio->nombre }}</span>
                        </a>
                        <button type="button" @click="$store.viaje.alternar({{ $s->id }}, @js($estado))" class="btn" aria-label="Quitar {{ $s->nombre }}">Quitar</button>
                    </div>
                @empty
                    <p class="text-gris">Nada por aquí todavía. <a href="{{ route('inicio') }}" wire:navigate class="font-bold text-pino underline">Explora sitios</a> y guárdalos desde su ficha.</p>
                @endforelse
            </section>
        @endforeach
    @endif
</div>
