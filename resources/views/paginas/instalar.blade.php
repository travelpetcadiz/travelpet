@php
    $ic = fn (string $n, string $c = 'size-5 text-pino') => svg('heroicon-o-'.$n, $c.' flex-none', ['aria-hidden' => 'true'])->toHtml();
    $paso = fn (int $n, string $texto) => '<li class="flex gap-3"><span class="grid size-8 flex-none place-items-center rounded-full bg-pino font-bold text-white">'.$n.'</span><span class="pt-1">'.$texto.'</span></li>';
    $boton = fn (string $icono, string $texto) => '<strong class="inline-flex items-center gap-1 align-bottom">'.$ic($icono).$texto.'</strong>';
@endphp
@component('layouts.app', [
    'title' => 'Añade TravelPet a tu móvil',
    'descripcion' => 'Cómo tener TravelPet en la pantalla de inicio del móvil, como una app, en iPhone y en Android.',
])
    <div class="mx-auto grid max-w-3xl gap-6 px-4 py-8">
        <header>
            <h1 class="text-3xl font-extrabold sm:text-4xl">Añade TravelPet a tu móvil</h1>
            <p class="mt-2 text-lg text-gris">Tendrás la guía a un toque, como una app, sin descargar nada de la tienda y sin ocupar espacio.</p>
        </header>

        <section class="tarjeta grid gap-4 p-4 sm:p-5" aria-labelledby="iphone">
            <h2 id="iphone" class="flex items-center gap-2.5 text-2xl font-bold">{!! $ic('device-phone-mobile', 'size-6 text-pino') !!}En iPhone</h2>
            <ol class="grid gap-3">
                {!! $paso(1, 'Abre <strong>'.e(request()->getHost()).'</strong> en Safari. Si vienes de Instagram o WhatsApp, ábrelo antes en Safari.') !!}
                {!! $paso(2, 'Pulsa '.$boton('arrow-up-on-square', 'Compartir').'. En algunas versiones está dentro del botón de los tres puntos.') !!}
                {!! $paso(3, 'Elige '.$boton('plus-circle', 'Añadir a pantalla de inicio').' y confirma con «Añadir».') !!}
            </ol>
        </section>

        {{-- En Android, Chrome deja instalarla con un botón (evento beforeinstallprompt); si no, se explica a mano --}}
        <section class="tarjeta grid gap-4 p-4 sm:p-5" aria-labelledby="android"
            x-data="{ aviso: null, instalada: false }"
            x-init="
                aviso = window.tpInstalar ?? null;
                window.addEventListener('beforeinstallprompt', e => { e.preventDefault(); aviso = e; });
                window.addEventListener('appinstalled', () => { instalada = true; aviso = null; });
            ">
            <h2 id="android" class="flex items-center gap-2.5 text-2xl font-bold">{!! $ic('device-phone-mobile', 'size-6 text-pino') !!}En Android</h2>
            <template x-if="aviso">
                <button type="button" class="btn btn-pri" @click="aviso.prompt(); aviso.userChoice.then(() => aviso = null)">{!! $ic('arrow-down-tray', 'size-5') !!}Añadir a mi móvil</button>
            </template>
            <p x-show="instalada" x-cloak class="rounded-xl bg-ok-suave px-4 py-3 text-ok" role="status">Listo: ya tienes TravelPet en tu móvil.</p>
            <p x-show="aviso" x-cloak class="text-sm text-gris">Si el botón no funciona, hazlo a mano:</p>
            <ol class="grid gap-3">
                {!! $paso(1, 'Abre <strong>'.e(request()->getHost()).'</strong> en Chrome.') !!}
                {!! $paso(2, 'Pulsa '.$boton('ellipsis-vertical', 'los tres puntos').' de arriba a la derecha.') !!}
                {!! $paso(3, 'Elige <strong>Instalar aplicación</strong> o <strong>Añadir a pantalla de inicio</strong>.') !!}
            </ol>
        </section>
    </div>
@endcomponent
