@php
    $menus = [
        ['heroicon-o-home', 'Inicio', 'Lo que tienes pendiente (reseñas, sugerencias, datos que caducan…) con un botón para ir directo a cada cosa.'],
        ['heroicon-o-map-pin', 'Sitios', 'Todos los lugares de la guía: hoteles, bares, playas, parques, veterinarios… Aquí los añades, los corriges, los confirmas o los ocultas.'],
        ['heroicon-o-photo', 'Fotos', 'Tus fotos reales para el diseño de la web. Súbelas y marca las que te gustaría para la portada.'],
        ['heroicon-o-chat-bubble-left-right', 'Reseñas', 'Opiniones que escribe la gente. No se ven en la web hasta que las publicas tú.'],
        ['heroicon-o-light-bulb', 'Sugerencias', 'Sitios nuevos que propone la gente y avisos de datos que están mal.'],
        ['heroicon-o-users', 'Usuarios', 'Las cuentas: quién puede entrar en este panel (administradores y editores) y quién solo usa la web.'],
        ['heroicon-o-clock', 'Historial', 'Cada cambio que se hace en un sitio: qué había antes, qué hay ahora, quién y cuándo. Sirve para deshacer un error.'],
        ['heroicon-o-building-office-2', 'Municipios y Provincias', 'Los pueblos y provincias de la guía. Solo hace falta tocarlos para abrir una provincia nueva.'],
    ];
    $recetas = [
        'Añadir un sitio nuevo' => [
            'Ve a <strong>Sitios</strong> y pulsa <strong>Añadir un sitio</strong> (arriba a la derecha).',
            'Rellena el nombre, el tipo y el municipio. Si el pueblo no está, pulsa el <strong>+</strong> que hay al lado.',
            'En <strong>Condiciones para mascotas</strong> pon si admite mascotas y lo que sepas: tamaño, suplemento, zonas…',
            'En <strong>Comprobación</strong> escribe de dónde sale el dato. Si te lo ha dicho el propio sitio, marca <strong>Confirmado por el propio sitio</strong>.',
            'Pulsa <strong>Guardar</strong>. Sale en la web al momento.',
        ],
        'Corregir un dato de un sitio' => [
            'Ve a <strong>Sitios</strong> y escribe el nombre en el buscador.',
            'Pulsa sobre el sitio, cambia lo que haga falta y pulsa <strong>Guardar cambios</strong>.',
            'El cambio queda apuntado en el <strong>Historial</strong>.',
        ],
        'Un sitio ha cerrado o ya no admite mascotas' => [
            'Si <strong>ya no admite mascotas</strong>: cambia «¿Admite mascotas?» a <strong>No</strong>. Así la gente lo sabe.',
            'Si <strong>ha cerrado</strong>: desactiva <strong>Se ve en la web</strong>. No lo borres: así no se pierde lo que sabías de él.',
        ],
        'Confirmar un sitio después de llamar' => [
            'En <strong>Sitios</strong>, busca el sitio y pulsa <strong>Confirmar</strong> en su fila.',
            'Se marca como confirmado con la fecha de hoy. Si algo ha cambiado, entra en el sitio y corrígelo antes.',
        ],
        'Publicar una reseña' => [
            'Ve a <strong>Reseñas</strong>. Salen primero las que esperan revisión.',
            'Léela. Si está bien, pulsa <strong>Publicar</strong>. Si es spam o insultante, pulsa <strong>No publicar</strong>.',
            'Si dice que no le dejaron entrar, revisa la ficha de ese sitio por si ha cambiado.',
        ],
        'Poner las fechas de una playa' => [
            'Abre la playa en <strong>Sitios</strong> y baja a <strong>Fechas en que se puede ir</strong>.',
            'Elige si se puede ir todo el año, solo entre unas fechas o si está prohibido entre unas fechas.',
            'Escribe los días como día/mes: <strong>01/06</strong> es el 1 de junio. Para el Domingo de Ramos, escribe <strong>ramos</strong>.',
        ],
        'Subir fotos' => [
            'Ve a <strong>Fotos</strong> y pulsa <strong>Subir una foto</strong>.',
            'Arrastra la foto, escribe qué se ve y dónde está hecha, y marca si te gustaría para la portada.',
        ],
        'Dar acceso a alguien que te ayude' => [
            'Ve a <strong>Usuarios</strong> y pulsa <strong>Añadir un usuario</strong> (o busca su cuenta si ya se registró en la web).',
            'Elige el rol <strong>Editor</strong>: podrá cambiar sitios, reseñas y sugerencias, pero no usuarios ni provincias.',
        ],
    ];
@endphp
<x-filament-panels::page>
    <x-filament::section icon="heroicon-o-bars-3" heading="Qué hay en cada menú">
        <dl style="display:grid;gap:1rem">
            @foreach ($menus as [$icono, $nombre, $texto])
                <div style="display:flex;gap:.75rem">
                    <x-filament::icon :icon="$icono" style="width:1.5rem;height:1.5rem;flex:none;color:#0E7F8A" />
                    <div>
                        <dt style="font-weight:700">{{ $nombre }}</dt>
                        <dd style="opacity:.8">{{ $texto }}</dd>
                    </div>
                </div>
            @endforeach
        </dl>
    </x-filament::section>

    <x-filament::section icon="heroicon-o-list-bullet" heading="Cómo se hace…" description="Pulsa en cada tarea para ver los pasos.">
        <div style="display:grid;gap:.75rem">
            @foreach ($recetas as $titulo => $pasos)
                <x-filament::section :heading="$titulo" collapsible collapsed compact>
                    <ol style="display:grid;gap:.5rem;list-style:decimal;padding-left:1.25rem">
                        @foreach ($pasos as $paso)
                            <li>{!! $paso !!}</li>
                        @endforeach
                    </ol>
                </x-filament::section>
            @endforeach
        </div>
    </x-filament::section>

    <x-filament::section icon="heroicon-o-chat-bubble-oval-left-ellipsis" heading="¿Algo más?">
        <p>Para cambios grandes (muchos sitios a la vez, el diseño de la web…) pídeselo a Claude en <strong>claude.ai/code</strong>, en el proyecto <strong>travelpet</strong>. Escríbele «¿Qué toca ahora?» y te guiará con el plan.</p>
    </x-filament::section>
</x-filament-panels::page>
