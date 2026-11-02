@php
    use App\Models\Sitio;

    // Sitio del boceto: el que llega desde los resultados o, si no, uno confirmado y con condiciones
    $s = Sitio::publicados()->with(['municipio.provincia', 'fotos' => fn ($f) => $f->publicadas()])
        ->when(request('sitio'), fn ($c, $slug) => $c->where('slug', $slug), fn ($c) => $c
            ->where('tipo', 'Alojamiento')->where('admite_perros', 'condiciones')->whereNotNull('latitud')
            ->orderByDesc('verificado')->orderByRaw('(tamano_maximo is not null) + (suplemento is not null) + (zonas_permitidas is not null) desc'))
        ->first() ?? Sitio::publicados()->with('municipio.provincia', 'fotos')->first();
@endphp
@component('layouts.app', ['title' => 'Boceto: ficha', 'noindex' => true])
    @include('mockup.partes.barra', ['actual' => 'ficha'])

    @if (! $s)
        <p class="mx-auto max-w-6xl p-8 text-gris">Aún no hay sitios para enseñar en el boceto.</p>
    @else
        @php
            $servicio = $s->esServicio() && $s->admite_perros !== 'no';
            $respuesta = match (true) {
                $servicio && $s->urgencias_24h => ['bg-mal-suave text-mal border-mal', 'Urgencias las 24 horas', 'lifebuoy', null],
                $servicio => ['bg-white text-tinta border-linea', $s->etiquetaTipo(), $s->icono(), null],
                $s->admite_perros === 'si' => ['bg-ok-suave text-ok border-ok', 'Sí, admite mascotas', 'check-circle', 'Puedes ir con tu mascota.'],
                $s->admite_perros === 'condiciones' => ['bg-aviso-suave text-aviso border-albero', 'Sí, con condiciones', 'exclamation-triangle', 'Mira las condiciones de abajo.'],
                $s->admite_perros === 'no' => ['bg-mal-suave text-mal border-mal', 'No admite mascotas', 'no-symbol', 'Busca otro sitio cerca.'],
                default => ['bg-cal text-gris border-linea', 'Aún no lo sabemos', 'question-mark-circle', 'Pregunta antes de ir.'],
            };
            $hoy = $s->hoy();

            // Condiciones con icono. Lo que no sabemos se dice, en gris, en lugar de esconderlo
            $condiciones = $servicio ? [
                ['clock', 'Horario', $s->temporada],
            ] : array_values(array_filter([
                ['scale', 'Tamaño o peso', $s->tamano_maximo ?: ($s->admite_grandes === null ? null : ($s->admite_grandes ? 'Admite perros grandes' : 'No admite perros grandes'))],
                ['banknotes', 'Suplemento', $s->suplemento ?: ($s->sin_suplemento === null ? null : ($s->sin_suplemento ? 'Sin suplemento' : 'Cobra suplemento'))],
                ['home', 'Dónde puede estar', $s->zonas_permitidas ?: ($s->puede_entrar === null ? null : ($s->puede_entrar ? 'Dentro del local' : 'Solo en la terraza'))],
                in_array($s->tipo, ['Playa', 'Espacio'], true) ? ['bolt', 'Correa', $s->sin_correa === null ? null : ($s->sin_correa ? 'Hay zona para ir sin correa' : 'Siempre con correa')] : null,
                ['calendar-days', $s->tipo === 'Playa' ? 'Temporada' : 'Horario y fechas', $s->temporada],
            ]));
            $sabemos = count(array_filter($condiciones, fn ($c) => filled($c[2])));

            $telefono = $s->urgencias_24h ? $s->telefonoUrgencias() : $s->telefono();
            $verTel = fn (string $tel) => implode(' ', str_split(substr($tel, 3), 3));
            $direccion = $s->direccion && ! str_contains(mb_strtolower($s->direccion), mb_strtolower($s->municipio->nombre))
                ? $s->direccion.', '.$s->municipio->nombre
                : ($s->direccion ?: $s->municipio->nombre);
            $comoLlegar = $s->latitud ? 'https://www.google.com/maps/dir/?api=1&destination='.$s->latitud.','.$s->longitud : null;
            $fotos = $s->fotos;
        @endphp

        {{-- pb-28: deja sitio a la barra de botones fija del móvil --}}
        <div class="pb-28 lg:pb-0">
            {{-- Foto del sitio o, si aún no hay, cabecera con el color de su categoría --}}
            @if ($fotos->isNotEmpty())
                <figure class="mx-auto max-w-6xl md:px-4 md:pt-4">
                    <img src="{{ $fotos->first()->url() }}" alt="{{ $fotos->first()->descripcion }}" fetchpriority="high" class="aspect-[16/10] w-full object-cover md:aspect-[21/9] md:rounded-2xl">
                    @if ($fotos->first()->origen !== 'propia')
                        <div class="px-4 pt-1 md:px-0"><x-credito-foto :foto="$fotos->first()" /></div>
                    @endif
                </figure>
            @endif

            <div class="mx-auto grid max-w-6xl gap-6 px-4 py-5 lg:grid-cols-[minmax(0,1fr)_360px]">
                <article class="grid content-start gap-5">
                    <header class="grid gap-2">
                        <a href="{{ route('mockup', 'resultados') }}?municipio={{ $s->municipio_id }}" class="inline-flex min-h-9 items-center gap-1 text-sm font-bold text-pino no-underline">@svg('heroicon-o-chevron-left', 'size-4', ['aria-hidden' => 'true'])Volver a {{ $s->municipio->nombre }}</a>
                        <span class="flex items-center gap-2 text-sm font-bold">
                            <x-icono-tipo :tipo="$s->tipo" />{{ $s->etiquetaTipo() }}@if ($s->subtipo)<span class="font-normal text-gris">· {{ $s->subtipo }}</span>@endif
                        </span>
                        <h1 class="text-3xl font-extrabold leading-tight sm:text-4xl">{{ $s->nombre }}</h1>
                        <p class="flex items-start gap-1.5 text-gris">@svg('heroicon-o-map-pin', 'mt-1 size-4 flex-none', ['aria-hidden' => 'true'])<span>{{ $direccion }}</span></p>
                    </header>

                    {{-- La respuesta, lo primero y fácil de ver, sin ocupar media pantalla --}}
                    <section class="flex items-start gap-3 rounded-2xl border-l-4 p-3.5 {{ $respuesta[0] }}">
                        @svg('heroicon-s-'.$respuesta[2], 'mt-0.5 size-7 flex-none', ['aria-hidden' => 'true'])
                        <div class="grid min-w-0 gap-1">
                            <p class="font-display text-xl font-extrabold leading-tight sm:text-2xl">{{ $respuesta[1] }}</p>
                            <p class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-tinta">
                                @if ($respuesta[3])<span>{{ $respuesta[3] }}</span>@endif
                                @if ($s->verificado)
                                    <x-confirmado />
                                @else
                                    <span class="sello bg-white text-gris">@svg('heroicon-s-shield-check', 'size-4', ['aria-hidden' => 'true'])Sin confirmar por el sitio</span>
                                @endif
                            </p>
                            @if ($hoy)
                                <p class="flex items-center gap-1.5 text-sm font-bold {{ $hoy['puede'] ? 'text-ok' : 'text-mal' }}">
                                    @svg('heroicon-s-calendar-days', 'size-4 flex-none', ['aria-hidden' => 'true']){{ $hoy['puede'] ? 'Hoy se puede ir' : 'Hoy no se puede ir' }}<span class="font-normal text-tinta">· {{ $hoy['texto'] }}</span>
                                </p>
                            @endif
                        </div>
                    </section>

                    {{-- Propuesta pendiente: «Qué animales admite» (solo si Natalia la aprueba) --}}
                    @unless ($servicio || $s->admite_perros === 'no')
                        <section class="grid gap-2 rounded-2xl border border-dashed border-albero bg-albero-suave/50 p-4">
                            <p class="text-sm font-bold text-aviso">Propuesta pendiente: «Qué animales admite». Así se vería si dices que sí.</p>
                            <ul class="flex flex-wrap gap-2">
                                <li class="sello bg-ok-suave px-3 py-1.5 text-sm text-ok">@svg('heroicon-s-check-circle', 'size-4', ['aria-hidden' => 'true'])Confirmado para perros</li>
                                <li class="sello bg-white px-3 py-1.5 text-sm text-gris">@svg('heroicon-s-question-mark-circle', 'size-4', ['aria-hidden' => 'true'])Gatos: aún no lo sabemos</li>
                                <li class="sello bg-white px-3 py-1.5 text-sm text-gris">@svg('heroicon-s-question-mark-circle', 'size-4', ['aria-hidden' => 'true'])Otros animales: aún no lo sabemos</li>
                            </ul>
                        </section>
                    @endunless

                    {{-- Condiciones con icono, en dos columnas en el móvil --}}
                    @if ($s->admite_perros !== 'no' && $condiciones)
                        <section>
                            <h2 class="mb-3 text-2xl font-extrabold">{{ $servicio ? 'Horario' : 'Condiciones' }}</h2>
                            <ul class="grid grid-cols-2 gap-3">
                                @foreach ($condiciones as [$icono, $etiqueta, $valor])
                                    <li @class(['tarjeta grid content-start gap-1.5 p-3', 'col-span-2' => filled($valor) && mb_strlen($valor) > 60])>
                                        <span class="flex items-center gap-2 text-sm font-bold {{ filled($valor) ? 'text-tinta' : 'text-gris' }}">
                                            <span class="grid size-9 flex-none place-items-center rounded-xl {{ filled($valor) ? 'bg-pino-suave text-pino' : 'bg-cal text-gris' }}">@svg('heroicon-o-'.$icono, 'size-5', ['aria-hidden' => 'true'])</span>
                                            {{ $etiqueta }}
                                        </span>
                                        @if (filled($valor))
                                            <span class="whitespace-pre-line text-tinta">{{ $valor }}</span>
                                        @else
                                            <span class="text-sm text-gris">Aún no lo sabemos. {{ $s->tipo === 'Alojamiento' ? 'Pregúntalo al reservar.' : 'Pregunta antes de ir.' }}</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </section>
                    @endif

                    @if ($s->otras_condiciones)
                        <section class="tarjeta flex gap-3 p-4">
                            @svg('heroicon-o-information-circle', 'size-6 flex-none text-pino', ['aria-hidden' => 'true'])
                            <div>
                                <h2 class="font-bold">Bueno saberlo</h2>
                                <p class="whitespace-pre-line text-tinta">{{ $s->otras_condiciones }}</p>
                            </div>
                        </section>
                    @endif

                    {{-- De dónde sale el dato --}}
                    <section class="flex gap-3 rounded-2xl border border-linea bg-white/60 p-4 text-sm text-gris">
                        @svg('heroicon-o-shield-check', 'size-6 flex-none text-pino', ['aria-hidden' => 'true'])
                        <div class="grid gap-1">
                            <p class="font-bold text-tinta">
                                {{ $s->verificado ? 'Confirmado por el propio sitio' : ($s->fuente_oficial ? 'Dato de una fuente oficial' : 'Dato de guías y webs') }}
                                @if ($s->fecha_comprobacion)
                                    <span class="font-normal text-gris">· comprobado el {{ $s->fecha_comprobacion->translatedFormat('j \d\e F \d\e Y') }}</span>
                                @endif
                            </p>
                            @if ($s->fuente)
                                <p>De dónde sale: {{ \Illuminate\Support\Str::limit($s->fuente, 120) }}</p>
                            @endif
                            <a href="#" class="inline-flex min-h-9 items-center gap-1.5 font-bold text-pino underline">@svg('heroicon-o-flag', 'size-4', ['aria-hidden' => 'true'])Avisar de un error</a>
                        </div>
                    </section>

                    {{-- Reseñas: en el boceto, la invitación a dejar la primera --}}
                    <section class="tarjeta grid gap-2 p-5">
                        <h2 class="flex items-center gap-2 text-2xl font-extrabold">@svg('heroicon-o-chat-bubble-left-right', 'size-6 text-pino', ['aria-hidden' => 'true'])Reseñas</h2>
                        <p class="text-gris">¿Has ido con tu mascota? Cuéntalo y ayudarás a quien vaya después.</p>
                        <a href="#" class="btn justify-self-start">@svg('heroicon-o-pencil-square', 'size-5', ['aria-hidden' => 'true'])Escribir una reseña</a>
                    </section>
                </article>

                {{-- En el ordenador: los botones y el mapa siempre a la vista, en la columna de la derecha --}}
                <aside class="hidden lg:block">
                    <div class="sticky top-[72px] grid gap-3">
                        <div class="tarjeta grid gap-2 p-4">
                            @if ($comoLlegar)
                                <a href="{{ $comoLlegar }}" target="_blank" rel="noopener" class="btn btn-albero min-h-12 text-lg">@svg('heroicon-o-map', 'size-5', ['aria-hidden' => 'true'])Cómo llegar</a>
                            @endif
                            @if ($telefono)
                                <a href="tel:{{ $telefono }}" class="btn min-h-12">@svg('heroicon-o-phone', 'size-5', ['aria-hidden' => 'true'])Llamar · {{ $verTel($telefono) }}</a>
                            @endif
                            <div class="grid grid-cols-2 gap-2">
                                <button type="button" class="btn">@svg('heroicon-o-bookmark', 'size-5', ['aria-hidden' => 'true'])Guardar</button>
                                <button type="button" class="btn">@svg('heroicon-o-share', 'size-5', ['aria-hidden' => 'true'])Compartir</button>
                            </div>
                        </div>
                        @if ($s->latitud)
                            <div class="relative grid h-56 place-items-center overflow-hidden rounded-2xl border border-linea bg-mar">
                                <div class="absolute inset-0 opacity-60" style="background-image: linear-gradient(var(--color-linea) 1px, transparent 1px), linear-gradient(90deg, var(--color-linea) 1px, transparent 1px); background-size: 40px 40px;"></div>
                                <span class="relative grid size-11 place-items-center rounded-full rounded-br-none border-2 border-white bg-white shadow-md rotate-45"><span class="-rotate-45"><x-icono-tipo :tipo="$s->tipo" class="size-6" /></span></span>
                                <p class="absolute inset-x-2 bottom-2 rounded-lg bg-white/90 px-2 py-1 text-xs text-gris">Boceto: aquí va el mapa pequeño de siempre.</p>
                            </div>
                        @endif
                    </div>
                </aside>
            </div>
        </div>

        {{-- En el móvil: barra fija abajo con lo que más se usa, al alcance del pulgar --}}
        <nav class="fixed inset-x-0 bottom-0 z-30 border-t border-linea bg-white/95 px-3 pb-[max(0.75rem,env(safe-area-inset-bottom))] pt-3 shadow-[0_-4px_16px_rgba(19,42,46,0.08)] backdrop-blur lg:hidden" aria-label="Acciones">
            <div class="mx-auto grid max-w-md grid-cols-[1.4fr_1fr_1fr] gap-2">
                @if ($comoLlegar)
                    <a href="{{ $comoLlegar }}" target="_blank" rel="noopener" class="btn btn-albero min-h-12 px-2">@svg('heroicon-o-map', 'size-5', ['aria-hidden' => 'true'])Cómo llegar</a>
                @else
                    <span class="btn min-h-12 px-2 text-gris">@svg('heroicon-o-map', 'size-5', ['aria-hidden' => 'true'])Sin mapa</span>
                @endif
                @if ($telefono)
                    <a href="tel:{{ $telefono }}" class="btn min-h-12 px-2">@svg('heroicon-o-phone', 'size-5', ['aria-hidden' => 'true'])Llamar</a>
                @else
                    <button type="button" class="btn min-h-12 px-2">@svg('heroicon-o-share', 'size-5', ['aria-hidden' => 'true'])Compartir</button>
                @endif
                <button type="button" class="btn min-h-12 px-2">@svg('heroicon-o-bookmark', 'size-5', ['aria-hidden' => 'true'])Guardar</button>
            </div>
        </nav>
    @endif
@endcomponent
