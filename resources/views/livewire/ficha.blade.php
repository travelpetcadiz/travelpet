@php
    use App\Models\Resena;
    $s = $sitio;
    $datos = [
        'Tamaño o peso máximo' => $s->tamano_maximo,
        'Suplemento' => $s->suplemento,
        'Zonas permitidas' => $s->zonas_permitidas,
        'Temporada' => $s->temporada,
        'Otras condiciones' => $s->otras_condiciones,
    ];
@endphp
<div class="mx-auto max-w-6xl px-4 py-6">
    <nav class="mb-4 text-sm text-gris" aria-label="Ruta">
        <a href="{{ route('inicio') }}" wire:navigate class="underline">Explorar</a> ›
        <a href="{{ route('inicio', ['provincia' => $s->municipio->provincia->slug]) }}" wire:navigate class="underline">{{ $s->municipio->provincia->nombre }}</a> ›
        <a href="{{ route('inicio', ['provincia' => $s->municipio->provincia->slug, 'municipio' => $s->municipio_id]) }}" wire:navigate class="underline">{{ $s->municipio->nombre }}</a> ›
        <span>{{ $s->nombre }}</span>
    </nav>

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_380px]">
        <article class="grid content-start gap-5">
            <header class="grid gap-2">
                <span class="flex items-center gap-2 text-sm font-bold">
                    <span class="size-2.5 rounded-full" style="background: {{ $s->color() }}"></span>
                    {{ $s->etiquetaTipo() }}@if ($s->subtipo)<span class="font-normal text-gris">· {{ $s->subtipo }}</span>@endif
                </span>
                <h1 class="text-3xl font-extrabold leading-tight sm:text-4xl">{{ $s->nombre }}</h1>
                <p class="text-gris">{{ $s->direccion ? $s->direccion.' · ' : '' }}{{ $s->municipio->nombre }}{{ $s->municipio->comarca ? ' · '.$s->municipio->comarca : '' }} · {{ $s->municipio->provincia->nombre }}</p>
                <div class="flex flex-wrap gap-2">
                    <x-admite :valor="$s->admite_perros" class="px-3 py-1 text-sm" />
                    @if ($s->verificado)
                        <span class="sello bg-ok-suave px-3 py-1 text-sm text-ok">✓ Verificado con el sitio</span>
                    @endif
                    @if ($media)
                        <span class="sello bg-albero-suave px-3 py-1 text-sm text-aviso">★ {{ number_format($media, 1, ',', '') }} · {{ $resenas->count() }} {{ $resenas->count() === 1 ? 'reseña' : 'reseñas' }}</span>
                    @endif
                </div>
            </header>

            @if ($s->esAntiguo())
                <p class="rounded-xl bg-aviso-suave p-3 text-sm text-aviso">Este dato se comprobó hace más de 6 meses. Confírmalo con el sitio antes de ir.</p>
            @endif

            <section class="tarjeta p-5">
                <h2 class="mb-3 text-xl font-bold">Condiciones para perros</h2>
                <dl class="grid gap-3">
                    @foreach ($datos as $etiqueta => $valor)
                        <div class="grid gap-0.5 sm:grid-cols-[200px_1fr] sm:gap-4">
                            <dt class="font-bold">{{ $etiqueta }}</dt>
                            <dd class="{{ $valor ? '' : 'text-gris' }}">{{ $valor ?: 'No consta' }}</dd>
                        </div>
                    @endforeach
                </dl>
            </section>

            @if ($s->contacto)
                <section class="tarjeta p-5">
                    <h2 class="mb-2 text-xl font-bold">Contacto</h2>
                    <div class="prosa">{!! enlazar($s->contacto) !!}</div>
                </section>
            @endif

            <section class="text-sm text-gris">
                <p><strong class="text-tinta">Fuente:</strong> {{ $s->fuente ?: 'No consta' }}@if ($s->fuente_oficial) <span class="sello bg-pino-suave text-pino">Oficial</span>@endif</p>
                <p><strong class="text-tinta">Comprobado:</strong> {{ $s->fecha_comprobacion?->translatedFormat('j \d\e F \d\e Y') ?? 'No consta' }}</p>
            </section>

            {{-- Reseñas --}}
            <section class="grid gap-4" id="resenas">
                <h2 class="text-2xl font-bold">Reseñas</h2>

                @forelse ($resenas as $r)
                    <article wire:key="r-{{ $r->id }}" class="tarjeta p-4">
                        <div class="flex flex-wrap items-center gap-2">
                            <strong>{{ $r->user->name }}</strong>
                            <span class="text-albero" aria-label="{{ $r->estrellas }} de 5 estrellas">{{ str_repeat('★', $r->estrellas) }}<span class="text-linea">{{ str_repeat('★', 5 - $r->estrellas) }}</span></span>
                            <span class="text-sm text-gris">{{ $r->created_at->translatedFormat('F Y') }}</span>
                        </div>
                        <p class="mt-1 text-sm">
                            @if ($r->pudo_entrar === 'si')
                                <span class="sello bg-ok-suave text-ok">Entró con su perro</span>
                            @else
                                <span class="sello bg-mal-suave text-mal">No le dejaron entrar</span>
                            @endif
                            @if ($r->tamano_perro) <span class="text-gris">· {{ Resena::TAMANOS[$r->tamano_perro] ?? '' }}</span> @endif
                        </p>
                        <p class="mt-2 whitespace-pre-line">{{ $r->texto }}</p>
                    </article>
                @empty
                    <p class="text-gris">Todavía no hay reseñas. ¿Has estado con tu perro? Cuéntalo.</p>
                @endforelse

                @if (session('resena'))
                    <p class="rounded-xl bg-ok-suave p-3 text-ok" role="status">{{ session('resena') }}</p>
                @endif

                @auth
                    <form wire:submit="enviarResena" class="tarjeta grid gap-4 p-5">
                        <h3 class="text-lg font-bold">{{ $miResena ? 'Actualiza tu reseña' : 'Deja tu reseña' }}</h3>
                        @if ($miResena?->estado === 'pendiente')
                            <p class="text-sm text-aviso">Tu reseña anterior está pendiente de revisión.</p>
                        @endif
                        <fieldset>
                            <legend class="etiqueta">Puntuación</legend>
                            <div class="flex gap-1">
                                @for ($i = 1; $i <= 5; $i++)
                                    <label class="cursor-pointer text-3xl {{ $estrellas >= $i ? 'text-albero' : 'text-linea' }}">
                                        <input type="radio" wire:model.live="estrellas" value="{{ $i }}" class="sr-only">
                                        <span aria-hidden="true">★</span><span class="sr-only">{{ $i }} estrellas</span>
                                    </label>
                                @endfor
                            </div>
                        </fieldset>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="pudo" class="etiqueta">¿Pudiste entrar con tu perro?</label>
                                <select id="pudo" wire:model="pudo_entrar" class="campo">
                                    <option value="si">Sí</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                            <div>
                                <label for="tam" class="etiqueta">Tamaño de tu perro</label>
                                <select id="tam" wire:model="tamano_perro" class="campo">
                                    <option value="">Prefiero no decirlo</option>
                                    @foreach (Resena::TAMANOS as $k => $v)
                                        <option value="{{ $k }}">{{ $v }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div>
                            <label for="texto" class="etiqueta">Tu experiencia</label>
                            <textarea id="texto" wire:model="texto" rows="4" class="campo" placeholder="¿Dónde os dejaron estar? ¿Pusieron agua? ¿Algún pero?"></textarea>
                            @error('texto') <p class="error">{{ $message }}</p> @enderror
                        </div>
                        <button class="btn btn-pri justify-self-start" wire:loading.attr="disabled">Enviar reseña</button>
                    </form>
                @else
                    <p class="tarjeta p-4"><a href="{{ route('entrar') }}" wire:navigate class="font-bold text-pino underline">Entra</a> o <a href="{{ route('registro') }}" wire:navigate class="font-bold text-pino underline">crea una cuenta</a> para dejar tu reseña.</p>
                @endauth
            </section>
        </article>

        <aside class="grid content-start gap-4">
            @if ($s->latitud)
                <div wire:ignore x-data="mapaSitio(@js($mapaCfg), {{ $s->longitud }}, {{ $s->latitud }}, @js($s->color()))"
                     class="h-64 overflow-hidden rounded-xl border border-linea"></div>
                <a class="btn" target="_blank" rel="noopener" href="https://www.google.com/maps/dir/?api=1&destination={{ $s->latitud }},{{ $s->longitud }}">Cómo llegar</a>
            @endif

            <div class="tarjeta grid gap-2 p-4">
                <h2 class="font-bold">Mi viaje</h2>
                @auth
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" wire:click="alternarViaje('quiero')" class="btn {{ $enViaje === 'quiero' ? 'btn-pri' : '' }}" aria-pressed="{{ $enViaje === 'quiero' ? 'true' : 'false' }}">♡ Quiero ir</button>
                        <button type="button" wire:click="alternarViaje('visitado')" class="btn {{ $enViaje === 'visitado' ? 'btn-pri' : '' }}" aria-pressed="{{ $enViaje === 'visitado' ? 'true' : 'false' }}">✓ He ido</button>
                    </div>
                @else
                    <p class="text-sm text-gris"><a href="{{ route('entrar') }}" wire:navigate class="font-bold text-pino underline">Entra</a> para guardar sitios en tu viaje.</p>
                @endauth
            </div>

            <div class="tarjeta grid gap-2 p-4">
                @if (session('error_enviado'))
                    <p class="text-sm text-ok" role="status">{{ session('error_enviado') }}</p>
                @endif
                @auth
                    @if ($mostrarError)
                        <form wire:submit="reportarError" class="grid gap-2">
                            <label for="err" class="etiqueta">¿Qué dato está mal?</label>
                            <textarea id="err" wire:model="error_mensaje" rows="3" class="campo" placeholder="Por ejemplo: ya no admiten perros en la terraza"></textarea>
                            @error('error_mensaje') <p class="error">{{ $message }}</p> @enderror
                            <div class="flex gap-2">
                                <button class="btn btn-pri">Enviar</button>
                                <button type="button" class="btn" wire:click="$set('mostrarError', false)">Cancelar</button>
                            </div>
                        </form>
                    @else
                        <button type="button" class="btn" wire:click="$set('mostrarError', true)">¿Este dato está mal?</button>
                    @endif
                @else
                    <p class="text-sm text-gris">¿Algún dato está mal? <a href="{{ route('entrar') }}" wire:navigate class="font-bold text-pino underline">Entra</a> y avísanos.</p>
                @endauth
            </div>

            @if ($cercanos->isNotEmpty())
                <div class="grid gap-2">
                    <h2 class="font-bold">Cerca de aquí</h2>
                    @foreach ($cercanos as $c)
                        <a href="{{ route('sitio', $c) }}" wire:navigate class="tarjeta flex items-center gap-3 p-3 no-underline hover:border-gris">
                            <span class="size-2.5 flex-none rounded-full" style="background: {{ $c->color() }}"></span>
                            <span class="min-w-0">
                                <strong class="block truncate">{{ $c->nombre }}</strong>
                                <span class="text-sm text-gris">{{ $c->etiquetaTipo() }} · {{ $c->municipio->nombre }}</span>
                            </span>
                        </a>
                    @endforeach
                </div>
            @endif

            @if (auth()->user()?->isStaff())
                <a href="{{ \App\Filament\Resources\Sitios\SitioResource::getUrl('edit', ['record' => $s]) }}" class="btn">Editar en el panel</a>
            @endif
        </aside>
    </div>
</div>
