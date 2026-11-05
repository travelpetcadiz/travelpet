@php
    use App\Models\Transporte;
    $ic = fn (string $n, string $c = 'size-5') => svg('heroicon-o-'.$n, $c.' flex-none', ['aria-hidden' => 'true'])->toHtml();
    $sello = fn (string $estado) => match ($estado) {
        'si' => ['bg-ok-suave text-ok', 'check-circle'],
        'condiciones' => ['bg-aviso-suave text-aviso', 'exclamation-triangle'],
        'no' => ['bg-mal-suave text-mal', 'no-symbol'],
        default => ['bg-cal text-gris', 'question-mark-circle'],
    };
@endphp
@component('layouts.app', [
    'title' => 'Transporte con mascota',
    'descripcion' => 'Cómo llegar a Cádiz con tu mascota: qué aerolíneas, trenes, autobuses y barcos la admiten, en qué condiciones y qué papeles necesitas.',
])
    <div class="mx-auto grid max-w-5xl gap-10 px-4 py-8">
        <header class="min-w-0">
            <h1 class="text-3xl font-extrabold sm:text-4xl">Transporte con mascota</h1>
            <p class="mt-1.5 font-display text-xl font-bold text-pino">Cómo llegar a Cádiz con tu mascota</p>
            <p class="mt-2 text-lg text-gris">Para venir a Cádiz o moverte por la provincia: qué compañías de avión, tren, autobús y barco admiten mascotas, en qué condiciones y qué papeles necesitas.</p>
            <nav class="-mx-4 mt-4 flex gap-2 overflow-x-auto px-4 pb-1" aria-label="Secciones de la página">
                @foreach (Transporte::MEDIOS as $medio => [$titulo, $icono])
                    @if ($grupos->has($medio))
                        <a href="#{{ $medio }}" class="chip">{!! $ic($icono, 'size-4') !!}{{ str($titulo)->after('En ')->ucfirst() }}</a>
                    @endif
                @endforeach
                <a href="#papeles" class="chip">{!! $ic('document-text', 'size-4') !!}Papeles</a>
            </nav>
            <p class="mt-4 flex items-start gap-2.5 rounded-xl bg-aviso-suave px-4 py-3 text-aviso">
                {!! svg('heroicon-s-exclamation-triangle', 'mt-0.5 size-5 flex-none', ['aria-hidden' => 'true'])->toHtml() !!}
                <span><strong class="text-tinta">Las normas cambian a menudo.</strong> Confírmalo con la compañía antes de comprar el billete. En cada una tienes el enlace a su norma.</span>
            </p>
        </header>

        @foreach (Transporte::MEDIOS as $medio => [$titulo, $icono])
            @continue(! $grupos->has($medio))
            <section id="{{ $medio }}" aria-labelledby="t-{{ $medio }}" class="min-w-0 scroll-mt-20"
                @if ($medio === 'avion') x-data="{ filtro: 'todas' }" @endif>
                <h2 id="t-{{ $medio }}" class="flex items-center gap-2.5 text-2xl font-bold">{!! $ic($icono, 'size-6 text-pino') !!}{{ $titulo }}</h2>
                @if ($medio === 'avion')
                    <p class="mt-1 text-gris">Aeropuerto de Jerez y los más cercanos: Sevilla y Málaga.</p>
                    <div class="-mx-4 mt-4 flex gap-2 overflow-x-auto px-4 pb-1" role="group" aria-label="Filtrar aerolíneas">
                        @foreach (['todas' => 'Todas', 'jerez' => 'Vuelan a Jerez', 'cabina' => 'Admiten en cabina'] as $valor => $texto)
                            <button type="button" class="chip" @click="filtro = '{{ $valor }}'" :aria-pressed="filtro === '{{ $valor }}'" aria-pressed="{{ $valor === 'todas' ? 'true' : 'false' }}">{{ $texto }}</button>
                        @endforeach
                    </div>
                @endif

                <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($grupos[$medio] as $t)
                        @php([$clase, $icn] = $sello($t->estado))
                        <article class="tarjeta flex flex-col p-4"
                            @if ($medio === 'avion') x-show="filtro === 'todas' || (filtro === 'jerez' && {{ $t->vuela_a_jerez ? 'true' : 'false' }}) || (filtro === 'cabina' && {{ $t->en_cabina ? 'true' : 'false' }})" @endif>
                            <h3 class="text-xl font-bold">{{ $t->nombre }}</h3>
                            @if ($t->zona)
                                <p class="text-sm text-gris">{{ $t->zona }}</p>
                            @endif
                            <p class="mt-2"><span class="sello {{ $clase }} text-sm">{!! svg('heroicon-s-'.$icn, 'size-4 flex-none', ['aria-hidden' => 'true'])->toHtml() !!}{{ $t->textoRespuesta() }}</span></p>

                            @if ($t->peso || $t->transportin || $t->precio)
                                <dl class="mt-3 grid gap-2.5 text-[15px]">
                                    @foreach ([['scale', 'Peso máximo', $t->peso], ['archive-box', 'Transportín', $t->transportin], ['banknotes', 'Precio', $t->precio]] as [$i, $k, $v])
                                        @if ($v)
                                            <div>
                                                <dt class="flex items-center gap-1.5 text-sm text-gris">{!! $ic($i, 'size-4 text-pino') !!}{{ $k }}</dt>
                                                <dd class="font-bold">{{ $v }}</dd>
                                            </div>
                                        @endif
                                    @endforeach
                                </dl>
                            @endif
                            @if ($t->otras_condiciones)
                                <p class="mt-3 text-[15px] text-gris">{{ $t->otras_condiciones }}</p>
                            @endif

                            <div class="mt-auto pt-3.5"><div class="flex flex-wrap items-center justify-between gap-x-2 border-t border-linea pt-2 text-sm text-gris">
                                <span>{{ $t->estado === 'no_consta' || ! $t->fecha_comprobacion ? 'Pendiente de comprobar' : 'Comprobado el '.$t->fecha_comprobacion->translatedFormat('j \d\e F \d\e Y') }}</span>
                                @if ($t->enlace)
                                    <a href="{{ $t->enlace }}" target="_blank" rel="noopener" class="inline-flex min-h-11 items-center gap-1.5 font-bold text-pino underline">{!! $ic('arrow-top-right-on-square', 'size-4') !!}Ver su norma</a>
                                @endif
                            </div></div>
                        </article>
                    @endforeach
                </div>
                @if ($medio === 'avion')
                    <p class="mt-3 text-[15px] text-gris">El peso incluye la mascota y el transportín. Las plazas son pocas: resérvala al comprar el billete.</p>
                @endif
            </section>
        @endforeach

        <section id="papeles" aria-labelledby="t-papeles" class="min-w-0 scroll-mt-20">
            <h2 id="t-papeles" class="flex items-center gap-2.5 text-2xl font-bold">{!! $ic('document-text', 'size-6 text-pino') !!}Papeles</h2>
            <p class="mt-1 text-gris">Para perros, gatos y hurones, según el Ministerio de Agricultura.</p>
            <ul class="tarjeta mt-4 divide-y divide-linea">
                @foreach ([
                    ['Microchip', 'Tu mascota tiene que llevarlo puesto y que se pueda leer.'],
                    ['Vacuna de la rabia en vigor', 'Se pone desde las 12 semanas de edad y vale a partir de los 21 días.'],
                    ['Pasaporte europeo para animales', 'Te lo hace tu veterinario. Es lo que piden para ir a otro país de la Unión Europea y a Ceuta.'],
                    ['Para ir fuera de la Unión Europea (por ejemplo, a Tánger)', 'Mira lo que pide ese país. Para volver, según de dónde vengas, piden un análisis de la rabia: la sangre se saca al menos 30 días después de la vacuna y luego hay que esperar 3 meses. Si te lo haces antes de salir, no tienes que esperar. Consúltalo con tu veterinario con tiempo.'],
                    ['Como mucho, 5 animales por persona', 'Si son más, cuenta como transporte de animales y hay otras normas.'],
                ] as [$titulo, $texto])
                    <li class="flex gap-3 p-4">
                        {!! svg('heroicon-s-check-circle', 'mt-0.5 size-5 flex-none text-ok', ['aria-hidden' => 'true'])->toHtml() !!}
                        <div><strong class="block">{{ $titulo }}</strong><span class="text-gris">{{ $texto }}</span></div>
                    </li>
                @endforeach
            </ul>
            <p class="mt-3 text-sm text-gris">Fuente: <a href="https://www.mapa.gob.es/es/ganaderia/temas/comercio-exterior-ganadero/desplazamiento-animales-compania/viajar-perros-gatos-hurones" target="_blank" rel="noopener" class="text-pino underline">Ministerio de Agricultura, «Viajar con la mascota»</a>, comprobado el 5 de noviembre de 2026.</p>
            <a href="{{ route('inicio') }}" wire:navigate class="btn btn-pri mt-5">{!! $ic('magnifying-glass') !!}Buscar sitios en Cádiz</a>
        </section>
    </div>
@endcomponent
