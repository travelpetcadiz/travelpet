@php
    // Bocetos del nuevo diseño: cada archivo de resources/views/mockup (salvo este) es una pantalla
    $pantallas = collect(glob(resource_path('views/mockup/*.blade.php')))
        ->map(fn ($f) => basename($f, '.blade.php'))
        ->reject(fn ($n) => $n === 'index')
        ->values();
    $fotos = \App\Models\Foto::latest()->get();
@endphp
@component('layouts.app', ['title' => 'Bocetos del nuevo diseño', 'noindex' => true])
    <div class="mx-auto grid max-w-4xl gap-8 px-4 py-8">
        <header>
            <h1 class="text-3xl font-extrabold">Bocetos del nuevo diseño</h1>
            <p class="mt-2 text-gris">Solo lo ve el equipo. Aquí aparecen las pantallas que prepara Claude para que las revises antes de cambiar la web de verdad. Ábrelas también en el móvil.</p>
        </header>

        <section>
            <h2 class="mb-3 text-xl font-bold">Pantallas</h2>
            @forelse ($pantallas as $p)
                <a href="{{ route('mockup', $p) }}" class="tarjeta mb-2 flex items-center gap-3 p-4 font-bold no-underline hover:border-gris">
                    @svg('heroicon-o-device-phone-mobile', 'size-6 text-pino', ['aria-hidden' => 'true']){{ \Illuminate\Support\Str::of($p)->replace('-', ' ')->ucfirst() }}
                </a>
            @empty
                <p class="tarjeta p-4 text-gris">Todavía no hay bocetos. Pídele a Claude: «Prepárame el boceto de la portada».</p>
            @endforelse
        </section>

        <section>
            <h2 class="mb-1 text-xl font-bold">Fotos subidas</h2>
            <p class="mb-3 text-sm text-gris">Se suben en el panel, en <a href="{{ url('/admin/fotos') }}" class="font-bold text-pino underline">Fotos</a>. Las marcadas con la estrella son las que te gustaría para la portada.</p>
            @if ($fotos->isEmpty())
                <p class="tarjeta p-4 text-gris">Aún no hay fotos.</p>
            @else
                <ul class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                    @foreach ($fotos as $f)
                        <li class="tarjeta overflow-hidden">
                            <img src="{{ $f->url() }}" alt="{{ $f->descripcion }}" class="aspect-[4/3] w-full object-cover">
                            <p class="flex items-start gap-1.5 p-2 text-sm">
                                @if ($f->para_portada) @svg('heroicon-s-star', 'mt-0.5 size-4 flex-none text-albero', ['aria-label' => 'Para la portada']) @endif
                                <span>{{ $f->descripcion }}@if ($f->lugar)<span class="block text-gris">{{ $f->lugar }}</span>@endif</span>
                            </p>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>
@endcomponent
