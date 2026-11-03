@php
    $menus = [
        ['heroicon-o-home', 'Inicio', 'Lo que tienes pendiente (reseñas, sugerencias, datos que caducan…) con un botón para ir directo a cada cosa.'],
        ['heroicon-o-map-pin', 'Sitios', 'Todos los lugares de la guía: hoteles, bares, playas, parques, veterinarios… Aquí los añades, los corriges, los confirmas o los ocultas.'],
        ['heroicon-o-photo', 'Fotos', 'Las fotos de los sitios (salen en su ficha) y las del diseño de la web. Ninguna se ve hasta que pulsas «Publicar».'],
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
        'Un servicio que va a domicilio (sin local)' => [
            'Abre el sitio en <strong>Sitios</strong> y, en <strong>Datos básicos</strong>, activa <strong>Servicio a domicilio</strong>.',
            'Desaparecen las casillas de ubicación: no hace falta ponerla. Al guardar, el sitio deja de salir en el mapa y ya no te lo pide como pendiente.',
            'En su ficha de la web sale «Servicio a domicilio» junto a su pueblo. Para ver todos los que tienes así, usa el embudo y elige <strong>Servicio a domicilio</strong>.',
        ],
        'Poner las fechas de una playa' => [
            'Abre la playa en <strong>Sitios</strong> y baja a <strong>Fechas en que se puede ir</strong>.',
            'Elige si se puede ir todo el año, solo entre unas fechas o si está prohibido entre unas fechas.',
            'Escribe los días como día/mes: <strong>01/06</strong> es el 1 de junio. Para el Domingo de Ramos, escribe <strong>ramos</strong>.',
        ],
        'Poner una foto a un sitio' => [
            'Abre el sitio en <strong>Sitios</strong> y baja hasta <strong>Fotos de este sitio</strong>.',
            'Pulsa <strong>Añadir una foto</strong> y elige de dónde sale: tuya, cedida por el sitio o de Wikimedia.',
            'Si es <strong>cedida</strong>, escribe cómo te dio permiso (por ejemplo, «correo del 3/11/2026»).',
            'Mira la foto: si <strong>no sale nadie reconocible</strong>, marca la casilla y activa <strong>Se ve en la web</strong>.',
            'La primera foto publicada es la principal. Para cambiar el orden, arrástralas.',
        ],
        'Usar una foto libre de Wikimedia' => [
            'En la ficha del sitio, en <strong>Fotos de este sitio</strong>, pulsa <strong>Buscar fotos libres en Wikimedia</strong>. Se guardan sin publicar.',
            'O busca tú en <strong>commons.wikimedia.org</strong>, abre la foto, copia su dirección y pégala en <strong>Añadir una foto › Wikimedia</strong>.',
            'Comprueba que es de verdad ese sitio y que no sale nadie reconocible, y pulsa <strong>Publicar</strong>.',
            'El autor y la licencia salen solos debajo de la foto. <strong>No la recortes ni le pongas filtros</strong>: la licencia lo complica.',
        ],
        'Fotos para el diseño de la web' => [
            'Ve a <strong>Fotos</strong> y pulsa <strong>Subir una foto</strong>, sin elegir sitio.',
            'Marca <strong>Me gustaría que saliera en la portada</strong> en tus favoritas.',
            'Para elegir el orden del carrusel, pon en <strong>Posición en la portada</strong> un 1 en la que quieras primera, un 2 en la segunda…',
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

    <x-filament::section icon="heroicon-o-envelope" heading="Correo para pedir fotos a un negocio"
        description="Cópialo, cambia lo que va entre [corchetes] y envíalo. Cuando contesten, guarda el correo: es tu permiso.">
        @php($correo = "Asunto: Fotos para vuestra ficha en TravelPet

Hola, [nombre del sitio]:

Soy Natalia, de TravelPet (travelpetcadiz.es), una guía gratuita de sitios de la provincia de Cádiz que admiten mascotas. Vuestra ficha ya está en la guía: [enlace a la ficha].

¿Nos podéis enviar una o dos fotos vuestras (fachada, terraza, habitación…) y darnos permiso para usarlas en vuestra ficha? Saldrán con vuestro nombre como autores.

Y ya que estamos, ¿nos confirmáis estos datos?
- ¿Admitís mascotas? ¿Hay límite de tamaño o peso?
- ¿Cobráis suplemento?
- ¿En qué zonas pueden estar?

Así la ficha saldrá como «Confirmado por el propio sitio».

¡Muchas gracias!
Natalia · TravelPet")
        <div x-data="{ copiado: false }" style="display:grid;gap:.75rem">
            <pre style="white-space:pre-wrap;font-family:inherit;font-size:.875rem;padding:1rem;border-radius:.75rem;border:1px solid rgb(0 0 0 / .1)">{{ $correo }}</pre>
            <div>
                <x-filament::button icon="heroicon-o-clipboard-document" color="gray"
                    x-on:click="navigator.clipboard.writeText(@js($correo)); copiado = true; setTimeout(() => copiado = false, 2500)">
                    <span x-text="copiado ? 'Copiado' : 'Copiar el correo'">Copiar el correo</span>
                </x-filament::button>
            </div>
        </div>
    </x-filament::section>

    <x-filament::section icon="heroicon-o-chat-bubble-oval-left-ellipsis" heading="¿Algo más?">
        <p>Para cambios grandes (muchos sitios a la vez, el diseño de la web…) pídeselo a Claude en <strong>claude.ai/code</strong>, en el proyecto <strong>travelpet</strong>. Escríbele «¿Qué toca ahora?» y te guiará con el plan.</p>
    </x-filament::section>
</x-filament-panels::page>
