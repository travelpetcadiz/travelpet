@php
    use App\Models\Sitio;

    // Rediseño de la ficha. Sin ?sitio=, enseña un alojamiento confirmado con condiciones para que salga de todo
    $s = request('sitio') ? Sitio::publicados()->find((int) request('sitio')) : null;
    $s ??= Sitio::publicados()->where('verificado', true)->where('tipo', 'Alojamiento')->whereNotNull('suplemento')->whereNotNull('tamano_maximo')->first()
        ?? Sitio::publicados()->firstOrFail();
    $s->load(['municipio.provincia', 'fotos' => fn ($f) => $f->publicadas()]);
    [$etiqueta, $color, $iconoTipo] = Sitio::TIPOS[$s->tipo];
    $servicio = $s->esServicio() && $s->admite_perros !== 'no';

    // [color, texto, icono]; las clases van enteras para que el diseño las encuentre
    $colores = [
        'mal' => ['border-mal', 'bg-mal-suave text-mal', 'text-mal'],
        'pino' => ['border-pino', 'bg-pino-suave text-pino', 'text-pino'],
        'ok' => ['border-ok', 'bg-ok-suave text-ok', 'text-ok'],
        'aviso' => ['border-albero', 'bg-albero-suave text-aviso', 'text-aviso'],
        'gris' => ['border-linea', 'bg-cal text-gris', 'text-gris'],
    ];
    $respuesta = match (true) {
        $servicio && $s->urgencias_24h => ['mal', 'Urgencias las 24 horas', 'lifebuoy'],
        $servicio => ['pino', 'Información útil', 'information-circle'],
        $s->admite_perros === 'si' => ['ok', 'Sí, admite mascotas', 'check-circle'],
        $s->admite_perros === 'condiciones' => ['aviso', 'Sí, con condiciones', 'exclamation-triangle'],
        $s->admite_perros === 'no' => ['mal', 'No admite mascotas', 'no-symbol'],
        default => ['gris', 'Aún no sabemos si admite mascotas', 'question-mark-circle'],
    };
    [$claseBorde, $claseCirculo, $claseTexto] = $colores[$respuesta[0]];
    // Las condiciones, cada una en su fila con icono; solo las que sabemos
    $datos = array_filter($servicio ? [
        'Horario' => ['clock', $s->temporada],
        'Información adicional' => ['information-circle', $s->otras_condiciones],
    ] : [
        'Tamaño o peso máximo' => ['scale', $s->tamano_maximo],
        'Suplemento' => ['banknotes', $s->suplemento],
        'Dónde puede estar' => ['home', $s->zonas_permitidas],
        'Temporada' => ['calendar-days', $s->temporada],
        'Información adicional' => ['information-circle', $s->otras_condiciones],
    ], fn ($d) => filled($d[1]));
    $hechos = array_filter([
        $s->admite_grandes === null ? null : ['scale', $s->admite_grandes ? 'Perros grandes' : 'Sin perros grandes'],
        $s->sin_suplemento === null ? null : ['banknotes', $s->sin_suplemento ? 'Sin suplemento' : 'Con suplemento'],
        $s->puede_entrar === null ? null : ['home', $s->puede_entrar ? 'Puede entrar dentro' : 'Solo terraza'],
        $s->sin_correa === null ? null : ['bolt', $s->sin_correa ? 'Zona sin correa' : 'Con correa'],
    ]);
    $telefono = $s->telefono();
    $hoy = $s->hoy();
    $foto = $s->fotos->first();
    $direccion = $s->direccion && ! str_contains(mb_strtolower($s->direccion), mb_strtolower($s->municipio->nombre))
        ? $s->direccion.', '.$s->municipio->nombre
        : ($s->direccion ?: $s->municipio->nombre);
@endphp
@component('layouts.app', ['title' => 'Boceto: rediseño de la ficha', 'noindex' => true])
    @include('mockup.partes.barra', ['actual' => 'rediseno-ficha'])

    {{-- Cabecera: la foto del sitio o, si no la hay, el color de su categoría --}}
    <header class="relative isolate overflow-hidden" style="background-color: {{ $color }}">
        @if ($foto)
            <img src="{{ $foto->url() }}" alt="{{ $foto->descripcion }}" class="absolute inset-0 -z-10 size-full object-cover">
            <div class="absolute inset-0 -z-10 bg-gradient-to-t from-tinta/90 via-tinta/40 to-transparent"></div>
        @else
            <div class="absolute inset-0 -z-10 bg-gradient-to-br from-black/0 to-black/30"></div>
            @svg('heroicon-o-'.$iconoTipo, 'absolute -right-8 -top-6 -z-10 size-56 text-white/10', ['aria-hidden' => 'true'])
        @endif
        <div class="mx-auto max-w-6xl px-4 pb-16 pt-5 text-white {{ $foto ? 'min-h-72 flex flex-col justify-end' : '' }}">
            <nav class="mb-6 text-sm text-white/80" aria-label="Ruta">
                <a href="{{ route('mockup', 'rediseno-lista') }}" class="underline">{{ $s->municipio->nombre }}</a> ·
                <a href="{{ route('mockup', 'rediseno-lista') }}?tipo={{ urlencode($s->tipo) }}" class="underline">{{ $etiqueta }}</a>
            </nav>
            <span class="inline-flex items-center gap-1.5 rounded-full bg-white/15 px-3 py-1 text-sm font-bold ring-1 ring-white/30">
                @svg('heroicon-s-'.$iconoTipo, 'size-4', ['aria-hidden' => 'true']){{ $s->subtipo ?: $etiqueta }}
            </span>
            <h1 class="mt-3 text-3xl font-extrabold leading-tight sm:text-5xl">{{ $s->nombre }}</h1>
            <p class="mt-2 flex items-start gap-1.5 text-white/90">@svg('heroicon-o-map-pin', 'mt-1 size-4 flex-none', ['aria-hidden' => 'true']){{ $direccion }}</p>
        </div>
    </header>

    <div class="mx-auto grid max-w-6xl gap-5 px-4 pb-28 lg:grid-cols-[minmax(0,1fr)_360px] lg:pb-12">
        <article class="relative z-10 -mt-10 grid content-start gap-5">
            {{-- La respuesta: tarjeta blanca que se monta sobre la cabecera, con el color de la respuesta a la izquierda --}}
            <section class="overflow-hidden rounded-3xl bg-white shadow-xl ring-1 ring-linea">
                <div class="flex items-center gap-3 border-l-8 p-5 {{ $claseBorde }}">
                    <span class="grid size-12 flex-none place-items-center rounded-full {{ $claseCirculo }}">@svg('heroicon-s-'.$respuesta[2], 'size-7', ['aria-hidden' => 'true'])</span>
                    <div>
                        <p class="font-display text-2xl font-extrabold leading-tight {{ $claseTexto }}">{{ $respuesta[1] }}</p>
                        @if ($s->verificado)
                            <p class="mt-1 inline-flex items-center gap-1 text-sm font-bold text-ok">@svg('heroicon-s-check-badge', 'size-4', ['aria-hidden' => 'true'])Confirmado por el propio sitio{{ $s->fecha_comprobacion ? ' el '.$s->fecha_comprobacion->translatedFormat('j \d\e F \d\e Y') : '' }}</p>
                        @else
                            <p class="mt-1 text-sm text-gris">Sin confirmar por el sitio. Pregúntalo al reservar.</p>
                        @endif
                    </div>
                </div>
                @if ($hoy)
                    <p class="mx-5 mb-4 flex items-start gap-2 rounded-xl p-3 {{ $hoy['puede'] ? 'bg-ok-suave text-ok' : 'bg-mal-suave text-mal' }}">
                        @svg('heroicon-s-calendar-days', 'mt-0.5 size-5 flex-none', ['aria-hidden' => 'true'])
                        <span><strong class="block">{{ $hoy['puede'] ? 'Hoy se puede ir' : 'Hoy no se puede ir' }}</strong><span class="text-tinta">{{ $hoy['texto'] }}</span></span>
                    </p>
                @endif
                @if ($hechos)
                    <ul class="grid grid-cols-2 gap-2 px-5 pb-5">
                        @foreach ($hechos as [$icono, $texto])
                            <li class="flex items-center gap-2 rounded-xl bg-cal px-3 py-2.5 text-sm font-bold">@svg('heroicon-o-'.$icono, 'size-5 flex-none text-pino', ['aria-hidden' => 'true']){{ $texto }}</li>
                        @endforeach
                    </ul>
                @endif
            </section>

            {{-- Las condiciones, una por fila --}}
            @if ($datos && ($servicio || $s->admite_perros !== 'no'))
                <section class="rounded-3xl bg-white p-5 ring-1 ring-linea">
                    <h2 class="text-xl font-extrabold">{{ $servicio ? 'Lo que debes saber' : 'Condiciones para tu mascota' }}</h2>
                    <dl class="mt-2 divide-y divide-linea">
                        @foreach ($datos as $nombre => [$icono, $valor])
                            <div class="flex gap-3 py-4">
                                <span class="grid size-10 flex-none place-items-center rounded-xl bg-pino-suave text-pino">@svg('heroicon-o-'.$icono, 'size-5', ['aria-hidden' => 'true'])</span>
                                <div>
                                    <dt class="text-sm font-bold text-gris">{{ $nombre }}</dt>
                                    <dd class="mt-0.5 whitespace-pre-line leading-snug">{{ $valor }}</dd>
                                </div>
                            </div>
                        @endforeach
                    </dl>
                </section>
            @endif

            <section class="rounded-3xl bg-white p-5 ring-1 ring-linea">
                <h2 class="flex items-center gap-2 text-xl font-extrabold">@svg('heroicon-o-chat-bubble-left-right', 'size-6 text-pino', ['aria-hidden' => 'true'])Reseñas</h2>
                <p class="mt-1 text-gris">¿Has estado con tu mascota? Cuéntalo y ayuda a otros.</p>
                <a href="#" class="btn mt-3">@svg('heroicon-o-pencil-square', 'size-5', ['aria-hidden' => 'true'])Escribir una reseña</a>
            </section>
        </article>

        {{-- Columna de acciones (en el móvil, debajo; los botones principales van fijos abajo) --}}
        <aside class="grid content-start gap-4 lg:sticky lg:top-24 lg:mt-6">
            <section class="rounded-3xl bg-white p-5 ring-1 ring-linea">
                <h2 class="text-lg font-extrabold">Contacto</h2>
                @if ($s->contacto)
                    <p class="mt-1 break-words text-gris">{{ $s->contacto }}</p>
                @endif
                <div class="mt-3 grid gap-2">
                    {{-- En el ordenador, aquí; en el móvil van en la barra fija de abajo --}}
                    @if ($s->latitud)
                        <a href="#" class="btn btn-albero hidden w-full lg:inline-flex">@svg('heroicon-o-map', 'size-5', ['aria-hidden' => 'true'])Cómo llegar</a>
                    @endif
                    <button type="button" class="btn hidden w-full lg:inline-flex">@svg('heroicon-o-bookmark', 'size-5', ['aria-hidden' => 'true'])Guardar</button>
                    @if ($telefono)
                        <a href="tel:{{ $telefono }}" class="btn w-full">@svg('heroicon-o-phone', 'size-5', ['aria-hidden' => 'true'])Llamar</a>
                    @endif
                    <button type="button" class="btn w-full">@svg('heroicon-o-share', 'size-5', ['aria-hidden' => 'true'])Compartir</button>
                    <a href="#" class="inline-flex min-h-11 items-center justify-center gap-2 text-sm font-bold text-gris underline">@svg('heroicon-o-flag', 'size-4', ['aria-hidden' => 'true'])Avisar de un error</a>
                </div>
            </section>
            <p class="flex items-start gap-2 px-1 text-sm text-gris">@svg('heroicon-o-shield-check', 'mt-0.5 size-4 flex-none', ['aria-hidden' => 'true'])<span>De dónde sale: {{ $s->fuente ?: 'aún sin fuente' }}</span></p>
        </aside>
    </div>

    {{-- Barra fija abajo en el móvil: lo que más se hace desde la ficha, siempre a mano --}}
    <div class="fixed inset-x-0 bottom-0 z-30 border-t border-linea bg-white/95 px-4 pb-[max(.75rem,env(safe-area-inset-bottom))] pt-3 shadow-[0_-4px_16px_rgba(19,42,46,.08)] backdrop-blur lg:hidden">
        <div class="mx-auto flex max-w-6xl gap-2">
            <button type="button" class="btn min-h-12 flex-none px-4">@svg('heroicon-o-bookmark', 'size-5', ['aria-hidden' => 'true'])Guardar</button>
            @if ($s->latitud)
                <a href="#" class="btn btn-albero min-h-12 flex-1">@svg('heroicon-o-map', 'size-5', ['aria-hidden' => 'true'])Cómo llegar</a>
            @elseif ($telefono)
                <a href="tel:{{ $telefono }}" class="btn btn-pri min-h-12 flex-1">@svg('heroicon-o-phone', 'size-5', ['aria-hidden' => 'true'])Llamar</a>
            @endif
        </div>
    </div>
@endcomponent
