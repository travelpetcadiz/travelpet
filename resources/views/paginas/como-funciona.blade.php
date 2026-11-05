@php
    $ic = fn (string $n, string $c = 'size-6') => svg('heroicon-o-'.$n, $c.' flex-none text-pino', ['aria-hidden' => 'true'])->toHtml();
    $pasos = [
        ['magnifying-glass', 'Busca', 'Escribe un pueblo o el nombre de un sitio, o toca una categoría: dormir, comer, playas, parques… Si ya estás de viaje, pulsa «Cerca de mí» y verás primero lo que tienes al lado.'],
        ['check-circle', 'Mira la respuesta', 'Cada sitio dice primero si admite mascotas y, debajo, las condiciones que conocemos: tamaño máximo, suplemento, zonas donde puede estar…'],
        ['phone', 'Confírmalo y ve', 'Desde la ficha puedes llamar, abrir la ruta en el mapa o compartirlo por WhatsApp. Si algo no lo sabemos, te lo decimos para que lo preguntes al reservar.'],
    ];
@endphp
@component('layouts.app', ['title' => 'Cómo funciona', 'descripcion' => 'Cómo buscar sitios que admiten mascotas, qué significa cada etiqueta y qué puedes hacer con una cuenta gratuita.'])
    <div class="mx-auto grid max-w-3xl gap-10 px-4 py-8">
        <header>
            <h1 class="text-3xl font-extrabold sm:text-4xl">Cómo funciona</h1>
            <p class="mt-2 text-lg text-gris">Una guía para saber, antes de ir, si puedes entrar con tu mascota y en qué condiciones.</p>
        </header>

        <section aria-labelledby="pasos">
            <h2 id="pasos" class="mb-4 text-2xl font-bold">En tres pasos</h2>
            <ol class="grid gap-3">
                @foreach ($pasos as $i => [$icono, $titulo, $texto])
                    <li class="tarjeta flex gap-4 p-4">
                        <span class="grid size-11 flex-none place-items-center rounded-full bg-pino-suave font-display text-lg font-extrabold text-pino">{{ $i + 1 }}</span>
                        <div>
                            <h3 class="flex items-center gap-2 text-lg font-bold">{!! $ic($icono, 'size-5') !!}{{ $titulo }}</h3>
                            <p class="mt-1 text-gris">{{ $texto }}</p>
                        </div>
                    </li>
                @endforeach
            </ol>
            <a href="{{ route('inicio') }}" wire:navigate class="btn btn-pri mt-4">{!! svg('heroicon-o-magnifying-glass', 'size-5', ['aria-hidden' => 'true'])->toHtml() !!}Empezar a buscar</a>
        </section>

        <section aria-labelledby="etiquetas">
            <h2 id="etiquetas" class="mb-4 text-2xl font-bold">Qué significa cada etiqueta</h2>
            <dl class="tarjeta divide-y divide-linea">
                @foreach ([
                    ['si', 'Puedes ir con tu mascota.'],
                    ['condiciones', 'Puedes ir, pero hay algo que tener en cuenta: un tamaño máximo, un suplemento o zonas a las que no puede pasar. Lo verás en la ficha.'],
                    ['no', 'No dejan entrar con mascotas.'],
                    ['no_consta', 'Todavía no lo sabemos. Pregunta antes de ir y, si te enteras, avísanos.'],
                ] as [$valor, $texto])
                    <div class="grid gap-1 p-4 sm:grid-cols-[220px_1fr] sm:gap-4">
                        <dt><x-admite :valor="$valor" class="px-2.5 py-1 text-sm" /></dt>
                        <dd class="text-gris">{{ $texto }}</dd>
                    </div>
                @endforeach
                <div class="grid gap-1 p-4 sm:grid-cols-[220px_1fr] sm:gap-4">
                    <dt><x-confirmado class="px-2.5 py-1 text-sm" /></dt>
                    <dd class="text-gris">Nos lo ha dicho el propio sitio. Si no aparece, el dato sale de su web, de su ayuntamiento o de otras guías, y conviene confirmarlo.</dd>
                </div>
            </dl>
            <p class="mt-3 flex items-start gap-2 text-sm text-gris">{!! $ic('heart', 'mt-0.5 size-4') !!}En veterinarios, peluquerías y otros servicios para mascotas no ponemos etiqueta: ahí tu mascota siempre es bienvenida. En su lugar verás el horario.</p>
        </section>

        <section aria-labelledby="utiles">
            <h2 id="utiles" class="mb-4 text-2xl font-bold">Lo más útil cuando ya estás de viaje</h2>
            <ul class="grid gap-3 sm:grid-cols-2">
                @foreach ([
                    ['map-pin', 'Cerca de mí', 'Ordena los sitios del más cercano al más lejano. Tu ubicación solo se usa en tu móvil para ordenar: no la guardamos.'],
                    ['calendar-days', '¿Se puede ir hoy?', 'Muchas playas solo admiten mascotas fuera de la temporada de baño. En su ficha te decimos si hoy se puede ir y desde cuándo.'],
                    ['lifebuoy', 'Urgencias veterinarias 24 h', 'Desde la portada, las clínicas con urgencias 24 h más cercanas. Llama antes de ir para que te esperen.'],
                    ['adjustments-horizontal', 'Filtros prácticos', 'En «Filtros» puedes pedir perros grandes, sin suplemento, dentro del local, zona sin correa o playas abiertas hoy.'],
                ] as [$icono, $titulo, $texto])
                    <li class="tarjeta p-4">
                        <h3 class="flex items-center gap-2 font-bold">{!! $ic($icono, 'size-5') !!}{{ $titulo }}</h3>
                        <p class="mt-1 text-sm text-gris">{{ $texto }}</p>
                    </li>
                @endforeach
            </ul>
        </section>

        <section aria-labelledby="cuenta">
            <h2 id="cuenta" class="mb-1 text-2xl font-bold">Con una cuenta gratis</h2>
            <p class="mb-4 text-gris">Para buscar y para guardar sitios en Mi viaje no hace falta cuenta: sin ella, lo guardado se queda en tu móvil. Con una cuenta, además, puedes:</p>
            <ul class="grid gap-3 sm:grid-cols-2">
                @foreach ([
                    ['bookmark', 'Tener Mi viaje en todos tus dispositivos', 'Los sitios a los que quieres ir y los que ya has visitado se guardan en tu cuenta y los ves en cualquier móvil u ordenador. Lo que guardaste antes de entrar se pasa solo a la cuenta.'],
                    ['chat-bubble-left-right', 'Dejar una reseña', 'Cuenta cómo os fue. La revisamos antes de publicarla, normalmente en un par de días.'],
                    ['flag', 'Avisar de un dato mal', 'Si un sitio ha cambiado sus normas, pulsa «¿Este dato está mal?» en su ficha.'],
                    ['light-bulb', 'Sugerir un sitio', 'Si conoces uno que no está, cuéntanos lo que sepas y lo comprobamos antes de añadirlo.'],
                ] as [$icono, $titulo, $texto])
                    <li class="tarjeta p-4">
                        <h3 class="flex items-center gap-2 font-bold">{!! $ic($icono, 'size-5') !!}{{ $titulo }}</h3>
                        <p class="mt-1 text-sm text-gris">{{ $texto }}</p>
                    </li>
                @endforeach
            </ul>
            @guest
                <a href="{{ route('registro') }}" wire:navigate class="btn btn-pri mt-4">{!! svg('heroicon-o-user-plus', 'size-5', ['aria-hidden' => 'true'])->toHtml() !!}Crear cuenta gratis</a>
            @endguest
        </section>

        <section aria-labelledby="dudas" class="grid gap-3">
            <h2 id="dudas" class="text-2xl font-bold">Dudas frecuentes</h2>
            @foreach ([
                ['¿Quién pone los datos?', 'Los comprobamos uno a uno: llamando o escribiendo al sitio, en su web o en la de su ayuntamiento. En cada ficha verás de dónde sale el dato y cuándo se comprobó.'],
                ['¿Cuesta algo?', 'No. La guía y la cuenta son gratis.'],
                ['Mi reseña no aparece', 'Las reseñas se revisan antes de publicarse para evitar spam. Si la cambias, vuelve a revisión.'],
                ['Un sitio dice que tiene datos antiguos', 'Si un dato tiene más de 6 meses, te avisamos en la ficha. Las normas cambian: confírmalo antes de ir.'],
            ] as [$pregunta, $respuesta])
                <details class="tarjeta group p-4">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-3 font-bold">
                        {{ $pregunta }}
                        {!! svg('heroicon-m-chevron-down', 'size-5 flex-none transition group-open:rotate-180', ['aria-hidden' => 'true'])->toHtml() !!}
                    </summary>
                    <p class="mt-2 text-gris">{{ $respuesta }}</p>
                </details>
            @endforeach
        </section>
    </div>
@endcomponent
