@php
    use App\Models\Resena;
    $s = $sitio;
    // Veterinarios y servicios para mascotas: no se dice si "admiten mascotas" y la temporada es su horario
    $servicio = $s->esServicio() && $s->admite_perros !== 'no';
    // etiqueta => [icono, valor]
    $datos = $servicio ? [
        'Horario' => ['clock', $s->temporada],
        'Información adicional' => ['information-circle', $s->otras_condiciones],
    ] : [
        'Tamaño o peso máximo' => ['scale', $s->tamano_maximo],
        'Suplemento' => ['banknotes', $s->suplemento],
        'Zonas permitidas' => ['home', $s->zonas_permitidas],
        'Temporada' => ['calendar-days', $s->temporada],
        'Información adicional' => ['information-circle', $s->otras_condiciones],
    ];
    $conocidos = array_filter($datos, fn ($d) => filled($d[1]));
    // Solo se avisa de lo que falta y importa para ese tipo de sitio
    $importan = match (true) {
        $servicio => ['Horario'],
        $s->tipo === 'Alojamiento' => ['Tamaño o peso máximo', 'Suplemento', 'Zonas permitidas'],
        default => ['Zonas permitidas'],
    };
    $faltan = array_values(array_filter($importan, fn ($k) => blank($datos[$k][1])));
    $respuesta = match (true) {
        $servicio && $s->urgencias_24h => ['bg-mal-suave text-mal border-mal', 'Urgencias las 24 horas', 'lifebuoy'],
        $servicio => ['bg-white text-tinta border-linea', 'Información útil', 'information-circle'],
        $s->admite_perros === 'si' => ['bg-ok-suave text-ok border-ok', 'Sí, admite mascotas', 'check-circle'],
        $s->admite_perros === 'condiciones' => ['bg-aviso-suave text-aviso border-aviso', 'Admite mascotas, con condiciones', 'exclamation-triangle'],
        $s->admite_perros === 'no' => ['bg-mal-suave text-mal border-mal', 'No admite mascotas', 'no-symbol'],
        default => ['bg-cal text-gris border-linea', 'Aún no sabemos si admite mascotas', 'question-mark-circle'],
    };
    $hoy = $s->hoy();
    // Datos clave con icono; solo los que sabemos (null = no lo sabemos y no se muestra)
    $hechos = array_filter([
        $s->admite_grandes === null ? null : ['scale', $s->admite_grandes ? 'Admite perros grandes' : 'No admite perros grandes', $s->admite_grandes],
        $s->sin_suplemento === null ? null : ['banknotes', $s->sin_suplemento ? 'Sin suplemento' : 'Con suplemento', $s->sin_suplemento],
        $s->puede_entrar === null ? null : ['home', $s->puede_entrar ? 'Puede estar dentro del local' : 'Solo en la terraza', $s->puede_entrar],
        $s->sin_correa === null ? null : ['bolt', $s->sin_correa ? 'Zona para ir sin correa' : 'Siempre con correa', $s->sin_correa],
    ]);

    // Teléfonos: el de urgencias (si lo hay y es distinto) y el normal, escritos como se leen en España
    $telefono = $s->telefono();
    $telUrgencias = $s->urgencias_24h ? $s->telefonoUrgencias() : null;
    if ($telUrgencias === $telefono) {
        $telefono = null;
    }
    $verTel = fn (string $tel) => implode(' ', str_split(substr($tel, 3), 3));
    // La caja de contacto solo si hay algo más que el teléfono (web, redes…)
    $otroContacto = trim(preg_replace('/(?:\+34[\s.]?)?[6789](?:[\s.]?\d){8}|[;·,\s]+/u', ' ', (string) $s->contacto)) !== '';
    // Dirección sin repetir el pueblo (la comarca y la provincia ya van en la ruta de arriba)
    $direccion = $s->direccion && ! str_contains(mb_strtolower($s->direccion), mb_strtolower($s->municipio->nombre))
        ? $s->direccion.', '.$s->municipio->nombre
        : ($s->direccion ?: $s->municipio->nombre);
    $textoCompartir = $s->nombre.' ('.$s->municipio->nombre.'): '.($servicio ? $s->etiquetaTipo() : mb_strtolower($respuesta[1])).'. Míralo en TravelPet:';
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
                    <x-icono-tipo :tipo="$s->tipo" />
                    {{ $s->etiquetaTipo() }}@if ($s->subtipo)<span class="font-normal text-gris">· {{ $s->subtipo }}</span>@endif
                </span>
                <h1 class="text-3xl font-extrabold leading-tight sm:text-4xl">{{ $s->nombre }}</h1>
                <p class="flex items-start gap-1.5 text-gris">@svg('heroicon-o-map-pin', 'mt-1 size-4 flex-none', ['aria-hidden' => 'true'])<span>@if ($s->a_domicilio)<strong class="text-tinta">Servicio a domicilio</strong> · @endif{{ $direccion }}</span></p>
                @if ($media)
                    <a href="#resenas" class="text-sm font-bold text-aviso no-underline">★ {{ number_format($media, 1, ',', '') }}, {{ $resenas->count() }} {{ $resenas->count() === 1 ? 'reseña' : 'reseñas' }}</a>
                @endif
            </header>

            @if ($s->esAntiguo())
                <p class="rounded-xl bg-aviso-suave p-3 text-sm text-aviso">Este dato se comprobó hace más de 6 meses. Confírmalo con el sitio antes de ir.</p>
            @endif

            {{-- Fotos: la principal grande y el resto en fila; siempre con su crédito --}}
            @if ($fotos->isNotEmpty())
                @php($principal = $fotos->first())
                <figure class="grid gap-2">
                    <img src="{{ $principal->url() }}" alt="{{ $principal->descripcion }}" class="aspect-[16/9] w-full rounded-2xl object-cover" fetchpriority="high">
                    <x-credito-foto :foto="$principal" />
                </figure>
                @if ($fotos->count() > 1)
                    <ul class="-mx-4 flex snap-x gap-3 overflow-x-auto px-4 pb-1 [scrollbar-width:thin]" aria-label="Más fotos">
                        @foreach ($fotos->skip(1) as $f)
                            <li class="w-56 flex-none snap-start">
                                <figure class="grid gap-1">
                                    <img src="{{ $f->url() }}" alt="{{ $f->descripcion }}" loading="lazy" class="aspect-[4/3] w-full rounded-xl object-cover">
                                    <x-credito-foto :foto="$f" />
                                </figure>
                            </li>
                        @endforeach
                    </ul>
                @endif
            @endif

            {{-- La respuesta a lo que viene a buscar la gente, lo primero (sin ocupar media pantalla) --}}
            <section class="rounded-2xl border p-4 {{ $respuesta[0] }}">
                <p class="flex items-center gap-2 font-display text-xl font-extrabold leading-tight">@svg('heroicon-s-'.$respuesta[2], 'size-6 flex-none', ['aria-hidden' => 'true']){{ $respuesta[1] }}</p>
                @if ($hoy)
                    <p class="mt-3 flex items-start gap-2 rounded-xl bg-white/70 p-3 text-tinta">
                        @svg('heroicon-s-calendar-days', 'mt-0.5 size-6 flex-none '.($hoy['puede'] ? 'text-ok' : 'text-mal'), ['aria-hidden' => 'true'])
                        <span><strong class="block text-lg {{ $hoy['puede'] ? 'text-ok' : 'text-mal' }}">{{ $hoy['puede'] ? 'Hoy se puede ir' : 'Hoy no se puede ir' }}</strong>{{ $hoy['texto'] }}</span>
                    </p>
                @endif
                @if ($hechos)
                    <ul class="mt-3 flex flex-wrap gap-2">
                        @foreach ($hechos as [$icono, $texto, $bueno])
                            <li class="sello bg-white px-3 py-1.5 text-sm {{ $bueno ? 'text-tinta' : 'text-gris' }}">@svg('heroicon-o-'.$icono, 'size-5', ['aria-hidden' => 'true']){{ $texto }}</li>
                        @endforeach
                    </ul>
                @endif
                @if ($conocidos && ($servicio || $s->admite_perros !== 'no'))
                    <dl class="mt-4 grid gap-4 border-t border-current/20 pt-4 text-tinta">
                        @foreach ($conocidos as $etiqueta => [$icono, $valor])
                            <div class="flex gap-3">
                                @svg('heroicon-o-'.$icono, 'mt-0.5 size-5 flex-none text-gris', ['aria-hidden' => 'true'])
                                <div>
                                    <dt class="font-bold">{{ $etiqueta }}</dt>
                                    <dd class="whitespace-pre-line">{{ $valor }}</dd>
                                </div>
                            </div>
                        @endforeach
                    </dl>
                @endif
                @if ($faltan && ($servicio || $s->admite_perros !== 'no'))
                    <p class="mt-3 text-sm text-gris">Aún no sabemos: {{ mb_strtolower(implode(', ', $faltan)) }}. {{ $servicio ? 'Llama antes de ir.' : 'Pregúntalo al reservar.' }}</p>
                @endif
            </section>

            <div class="flex flex-wrap gap-2">
                @if ($telUrgencias)
                    <a class="btn w-full border-mal bg-mal text-white hover:brightness-110 sm:w-auto" href="tel:{{ $telUrgencias }}">@svg('heroicon-o-phone', 'size-5', ['aria-hidden' => 'true'])Llamar a urgencias · {{ $verTel($telUrgencias) }}</a>
                @endif
                @if ($telefono)
                    <a class="btn w-full sm:w-auto" href="tel:{{ $telefono }}">@svg('heroicon-o-phone', 'size-5', ['aria-hidden' => 'true']){{ $telUrgencias ? 'Llamar a la clínica' : 'Llamar' }} · {{ $verTel($telefono) }}</a>
                @endif
                @if ($s->latitud && ! $s->a_domicilio)
                    <a class="btn btn-albero flex-1 sm:flex-none" target="_blank" rel="noopener" href="https://www.google.com/maps/dir/?api=1&destination={{ $s->latitud }},{{ $s->longitud }}">@svg('heroicon-o-map', 'size-5', ['aria-hidden' => 'true'])Cómo llegar</a>
                @endif
                {{-- En el móvil abre el menú de compartir (WhatsApp, etc.); si no lo hay, WhatsApp directamente --}}
                <button type="button" class="btn flex-1 sm:flex-none"
                    x-data="{ texto: @js($textoCompartir), url: @js(route('sitio', $s)) }"
                    @click="navigator.share
                        ? navigator.share({ title: @js($s->nombre), text: texto, url }).catch(() => {})
                        : window.open('https://wa.me/?text=' + encodeURIComponent(texto + ' ' + url), '_blank', 'noopener')">
                    @svg('heroicon-o-share', 'size-5', ['aria-hidden' => 'true'])Compartir
                </button>
                {{-- Guardar en Mi viaje: sin cuenta se queda en el móvil (resources/js/viaje.js) --}}
                <button type="button" x-data @click="$store.viaje.alternar({{ $s->id }}, 'quiero')"
                    :class="$store.viaje.estado({{ $s->id }}) === 'quiero' && 'btn-pri'" :aria-pressed="$store.viaje.estado({{ $s->id }}) === 'quiero'"
                    class="btn flex-1 sm:flex-none" aria-pressed="false">
                    <span x-show="$store.viaje.estado({{ $s->id }}) !== 'quiero'" class="inline-flex items-center gap-2">@svg('heroicon-o-bookmark', 'size-5', ['aria-hidden' => 'true'])Guardar</span>
                    <span x-show="$store.viaje.estado({{ $s->id }}) === 'quiero'" x-cloak class="inline-flex items-center gap-2">@svg('heroicon-s-bookmark', 'size-5', ['aria-hidden' => 'true'])Guardado</span>
                </button>
            </div>

            @if ($otroContacto)
                <section class="tarjeta p-5">
                    <h2 class="mb-2 flex items-center gap-2 text-lg font-bold">@svg('heroicon-o-globe-alt', 'size-5 text-pino', ['aria-hidden' => 'true'])Web y contacto</h2>
                    <div class="prosa">{!! enlazar($s->contacto) !!}</div>
                </section>
            @endif

            {{-- De dónde sale el dato: lo que da confianza a la guía --}}
            <section class="flex gap-3 rounded-xl border border-linea bg-white/60 p-4 text-sm text-gris">
                @svg('heroicon-o-shield-check', 'size-5 flex-none text-pino', ['aria-hidden' => 'true'])
                <div class="grid gap-1">
                    <p class="font-bold text-tinta">
                        @if ($s->verificado)
                            Confirmado por el propio sitio
                        @elseif ($s->fuente_oficial)
                            Dato de una fuente oficial
                        @else
                            Dato de guías y webs
                        @endif
                        @if ($s->fecha_comprobacion)
                            <span class="font-normal text-gris">· comprobado el {{ $s->fecha_comprobacion->translatedFormat('j \d\e F \d\e Y') }}</span>
                        @endif
                    </p>
                    @if ($s->fuente)
                        <div class="prosa">De dónde sale: {!! enlazar($s->fuente) !!}</div>
                    @endif
                    @unless ($s->verificado)
                        <p>Confírmalo con el sitio antes de ir.</p>
                    @endunless
                </div>
            </section>

            {{-- Reseñas --}}
            <section class="grid gap-4" id="resenas">
                <h2 class="flex items-center gap-2 text-2xl font-bold">@svg('heroicon-o-chat-bubble-left-right', 'size-6 text-pino', ['aria-hidden' => 'true'])Reseñas</h2>

                @forelse ($resenas as $r)
                    <article wire:key="r-{{ $r->id }}" class="tarjeta p-4">
                        <div class="flex flex-wrap items-center gap-2">
                            <strong>{{ $r->user->name }}</strong>
                            <span class="text-albero" aria-label="{{ $r->estrellas }} de 5 estrellas">{{ str_repeat('★', $r->estrellas) }}<span class="text-linea">{{ str_repeat('★', 5 - $r->estrellas) }}</span></span>
                            <span class="text-sm text-gris">{{ $r->created_at->translatedFormat('F Y') }}</span>
                        </div>
                        <p class="mt-1 text-sm">
                            @if ($r->pudo_entrar === 'si')
                                <span class="sello bg-ok-suave text-ok">@svg('heroicon-s-check-circle', 'size-4', ['aria-hidden' => 'true'])Entró con su mascota</span>
                            @else
                                <span class="sello bg-mal-suave text-mal">@svg('heroicon-s-no-symbol', 'size-4', ['aria-hidden' => 'true'])No le dejaron entrar</span>
                            @endif
                            @if ($r->tamano_perro) <span class="text-gris">· {{ Resena::TAMANOS[$r->tamano_perro] ?? '' }}</span> @endif
                        </p>
                        <p class="mt-2 whitespace-pre-line">{{ $r->texto }}</p>
                    </article>
                @empty
                    <p class="text-gris">Todavía no hay reseñas. ¿Has estado con tu mascota? Cuéntalo.</p>
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
                                <label for="pudo" class="etiqueta">¿Pudiste entrar con tu mascota?</label>
                                <select id="pudo" wire:model="pudo_entrar" class="campo">
                                    <option value="si">Sí</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                            <div>
                                <label for="tam" class="etiqueta">Tamaño de tu mascota</label>
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
                        <p class="text-sm text-gris">Tu nombre de la cuenta saldrá junto a la reseña cuando la publiquemos; tu correo, nunca. Cuenta tu experiencia sin datos personales de nadie (ver <a href="{{ route('legal.condiciones') }}" class="underline">condiciones de uso</a>).</p>
                        <button class="btn btn-pri justify-self-start" wire:loading.attr="disabled">@svg('heroicon-o-paper-airplane', 'size-5', ['aria-hidden' => 'true'])Enviar reseña</button>
                    </form>
                @else
                    <p class="tarjeta p-4"><a href="{{ route('entrar') }}" wire:navigate class="font-bold text-pino underline">Entra</a> o <a href="{{ route('registro') }}" wire:navigate class="font-bold text-pino underline">crea una cuenta</a> para dejar tu reseña.</p>
                @endauth
            </section>
        </article>

        <aside class="grid content-start gap-4">
            @if ($s->latitud && ! $s->a_domicilio)
                <div wire:ignore x-data="mapaSitio(@js($mapaCfg), {{ $s->longitud }}, {{ $s->latitud }}, @js($s->color()))"
                     class="h-64 overflow-hidden rounded-xl border border-linea"></div>
            @endif

            <div class="tarjeta grid gap-2 p-4">
                <h2 class="flex items-center gap-2 font-bold">@svg('heroicon-o-bookmark', 'size-5 text-pino', ['aria-hidden' => 'true'])Mi viaje</h2>
                <p class="text-sm text-gris">Apunta los sitios a los que quieres ir y los que ya has visitado con tu mascota.</p>
                <div class="grid grid-cols-2 gap-2" x-data="{ id: {{ $s->id }} }">
                    <button type="button" @click="$store.viaje.alternar(id, 'quiero')" class="btn" :class="$store.viaje.estado(id) === 'quiero' && 'btn-pri'" :aria-pressed="$store.viaje.estado(id) === 'quiero'" aria-pressed="false">@svg('heroicon-o-heart', 'size-5', ['aria-hidden' => 'true'])Quiero ir</button>
                    <button type="button" @click="$store.viaje.alternar(id, 'visitado')" class="btn" :class="$store.viaje.estado(id) === 'visitado' && 'btn-pri'" :aria-pressed="$store.viaje.estado(id) === 'visitado'" aria-pressed="false">@svg('heroicon-o-check', 'size-5', ['aria-hidden' => 'true'])He ido</button>
                </div>
                <a href="{{ route('viaje') }}" wire:navigate class="text-sm font-bold text-pino underline">Ver Mi viaje</a>
            </div>

            <div class="tarjeta grid gap-2 p-4">
                @if (session('error_enviado'))
                    <p class="text-sm text-ok" role="status">{{ session('error_enviado') }}</p>
                @endif
                @auth
                    @if ($mostrarError)
                        <form wire:submit="reportarError" class="grid gap-2">
                            <label for="err" class="etiqueta">¿Qué dato está mal?</label>
                            <textarea id="err" wire:model="error_mensaje" rows="3" class="campo" placeholder="Por ejemplo: ya no admiten mascotas en la terraza"></textarea>
                            @error('error_mensaje') <p class="error">{{ $message }}</p> @enderror
                            <div class="flex gap-2">
                                <button class="btn btn-pri">@svg('heroicon-o-paper-airplane', 'size-5', ['aria-hidden' => 'true'])Enviar aviso</button>
                                <button type="button" class="btn" wire:click="$set('mostrarError', false)">Cancelar</button>
                            </div>
                        </form>
                    @else
                        <button type="button" class="btn" wire:click="$set('mostrarError', true)">@svg('heroicon-o-flag', 'size-5', ['aria-hidden' => 'true'])¿Este dato está mal?</button>
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
                            <x-icono-tipo :tipo="$c->tipo" class="size-5" />
                            <span class="min-w-0">
                                <strong class="block truncate">{{ $c->nombre }}</strong>
                                <span class="text-sm text-gris">{{ $c->etiquetaTipo() }} · {{ $c->municipio->nombre }}</span>
                            </span>
                        </a>
                    @endforeach
                </div>
            @endif

            @if (auth()->user()?->isStaff())
                <a href="{{ \App\Filament\Resources\Sitios\SitioResource::getUrl('edit', ['record' => $s]) }}" class="btn">@svg('heroicon-o-pencil-square', 'size-5', ['aria-hidden' => 'true'])Editar en el panel</a>
            @endif
        </aside>
    </div>
</div>
