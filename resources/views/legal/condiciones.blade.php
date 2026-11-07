@php($t = config('travelpet.titular'))
@component('layouts.app', ['title' => 'Condiciones de uso', 'descripcion' => 'Normas para usar TravelPet, tener una cuenta y publicar reseñas, sugerencias y fotos.'])
    <x-pagina-legal titulo="Condiciones de uso">
        <p>Estas son las normas para usar TravelPet. Usar la guía significa aceptarlas; si creas una cuenta, las aceptas expresamente al registrarte.</p>

        <h2>La guía</h2>
        <ul>
            <li>TravelPet es gratuita. La información es orientativa: confírmala con el sitio antes de ir (lo explicamos en el <a href="{{ route('legal.aviso') }}">aviso legal</a>).</li>
            <li>Puedes consultar y compartir las fichas libremente. No está permitido copiar la guía de forma masiva ni usar programas para descargar su contenido sin nuestro permiso.</li>
        </ul>

        <h2>Tu cuenta</h2>
        <ul>
            <li>Necesitas tener al menos 14 años.</li>
            <li>La cuenta es personal: usa tus datos reales y no compartas tu contraseña.</li>
            <li>El nombre que pongas se verá junto a tus reseñas. No uses el nombre de otra persona ni de un negocio que no sea tuyo.</li>
            <li>Puedes borrar tu cuenta cuando quieras escribiéndonos a <a href="mailto:{{ $t['email'] }}">{{ $t['email'] }}</a>.</li>
        </ul>

        <h2>Lo que publicas: reseñas, sugerencias y fotos</h2>
        <h3>Qué te pedimos</h3>
        <ul>
            <li>Que cuentes <strong>tu propia experiencia</strong>, de forma honrada.</li>
            <li>Que sea sobre el sitio y sobre ir con mascota: no publiques datos personales de nadie (nombres de empleados, teléfonos particulares, matrículas…).</li>
            <li>Nada de insultos, amenazas, contenido discriminatorio, publicidad, enlaces de spam ni reseñas a cambio de dinero o regalos.</li>
            <li>Si tienes o trabajas en un negocio, no reseñes el tuyo ni el de la competencia. Para corregir tu ficha, usa la página <a href="{{ route('negocios') }}">para negocios</a>.</li>
            <li>Si algún día subes fotos: que sean tuyas y que no salga ninguna persona que se pueda reconocer sin su permiso.</li>
        </ul>
        <h3>Revisión</h3>
        <p>Revisamos las reseñas antes de publicarlas. Podemos no publicar, editar para quitar datos personales o retirar cualquier contenido que no cumpla estas normas. Si un sitio nos demuestra que una reseña es falsa, la retiraremos.</p>
        <h3>Permiso para publicarlo</h3>
        <p>Lo que escribes sigue siendo tuyo. Al enviarlo nos das permiso, gratuito y sin exclusividad, para publicarlo en TravelPet (en la web y al compartir sus fichas) mientras esté publicado. Si borras tu cuenta, tus reseñas se borran con ella.</p>
        <h3>Responsabilidad</h3>
        <p>Cada persona responde de lo que publica. Si ves algo que no debería estar, avísanos a <a href="mailto:{{ $t['email'] }}">{{ $t['email'] }}</a> y lo revisaremos enseguida.</p>

        <h2>Cuándo podemos cerrar una cuenta</h2>
        <p>Si alguien incumple estas normas de forma grave o repetida (spam, reseñas falsas, insultos), podemos desactivar su cuenta. Se lo diremos por correo, y podrá contestarnos si cree que es un error.</p>

        <h2>Cambios en estas condiciones</h2>
        <p>Si las cambiamos, lo indicaremos aquí con la fecha de actualización. Si el cambio es importante, lo avisaremos en la portada de la web.</p>

        <h2>Ley aplicable</h2>
        <p>Estas condiciones se rigen por la ley española. Si eres consumidor, para cualquier conflicto puedes acudir a los juzgados de tu domicilio.</p>
    </x-pagina-legal>
@endcomponent
