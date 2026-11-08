@php($t = config('travelpet.titular'))
@component('layouts.app', ['title' => 'Aviso legal', 'descripcion' => 'Quién está detrás de TravelPet, para qué sirve la guía y hasta dónde llega nuestra responsabilidad.'])
    <x-pagina-legal titulo="Aviso legal">
        <h2>Quién está detrás de TravelPet</h2>
        <p>Cumpliendo la Ley 34/2002 de servicios de la sociedad de la información (LSSI), estos son los datos del titular de travelpetcadiz.es:</p>
        <ul>
            <li><strong>Titular:</strong> {{ $t['nombre'] }}</li>
            @if ($t['nif'])
                <li><strong>NIF:</strong> {{ $t['nif'] }}</li>
            @endif
            @if ($t['domicilio'])
                <li><strong>Domicilio:</strong> {{ $t['domicilio'] }}</li>
            @endif
            <li><strong>Correo:</strong> <a href="mailto:{{ $t['email'] }}">{{ $t['email'] }}</a></li>
        </ul>

        <h2>Qué es TravelPet</h2>
        <p>TravelPet es una guía gratuita de sitios que admiten mascotas: alojamientos, bares y restaurantes, playas, parques, veterinarios y otros servicios. Para usarla no hace falta cuenta. Con una cuenta gratuita puedes, además, dejar reseñas, avisar de datos incorrectos y sugerir sitios.</p>
        <p><strong>No vendemos nada ni hacemos reservas.</strong> Los sitios no pagan por aparecer en la guía y la etiqueta «Confirmado por el propio sitio» no se puede comprar: solo indica que el sitio nos ha confirmado sus datos.</p>

        <h2>La información de la guía puede cambiar</h2>
        <p>Comprobamos cada dato y en cada ficha decimos de dónde sale y cuándo lo comprobamos. Aun así, los sitios cambian sus normas sin avisar, y las playas y los parques dependen de ordenanzas que se modifican cada temporada. Por eso:</p>
        <ul>
            <li>La información es <strong>orientativa</strong>. Antes de ir, confírmala con el propio sitio, sobre todo si la ficha avisa de que el dato es antiguo o no está confirmado.</li>
            <li>No respondemos de que un sitio te deje o no entrar con tu mascota, ni de las condiciones o precios que te aplique: eso depende de cada sitio.</li>
            <li>En urgencias veterinarias, llama siempre antes de ir para que te esperen.</li>
        </ul>
        <p>Si ves un dato mal, avísanos desde la ficha («¿Este dato está mal?») o escribiéndonos. Lo corregimos lo antes posible.</p>

        <h2>Si tienes un negocio que sale en la guía</h2>
        <p>Puedes pedirnos que corrijamos tus datos, que los confirmemos o que retiremos tu ficha escribiéndonos a <a href="mailto:{{ $t['email'] }}">{{ $t['email'] }}</a> o desde la página <a href="{{ route('negocios') }}">para negocios</a>.</p>

        <h2>Propiedad intelectual</h2>
        <ul>
            <li>Los textos, el diseño, el logotipo y la selección y organización de la información de TravelPet son nuestros. Puedes enlazarlos y compartirlos, pero no copiar la guía ni reutilizar su contenido de forma masiva sin permiso.</li>
            <li>El mapa usa datos de © los colaboradores de <a href="https://www.openstreetmap.org/copyright" rel="noopener">OpenStreetMap</a>, con licencia ODbL.</li>
            <li>Los nombres y marcas de los sitios son de sus titulares; los citamos solo para informar.</li>
        </ul>

        <h2>Fotos</h2>
        <p>Las fotos son de sus autores, que se indican debajo de cada una. Algunas son del equipo de TravelPet, otras nos las han cedido los propios sitios y otras tienen licencia libre (Wikimedia Commons), con su autor y su licencia enlazados. Si eres autor de una foto, sales en ella y quieres que la retiremos, escríbenos a <a href="mailto:{{ $t['email'] }}">{{ $t['email'] }}</a> y la quitaremos.</p>

        <h2>Enlaces a otras webs</h2>
        <p>Enlazamos a las webs de los sitios, a Google Maps para «Cómo llegar» y a WhatsApp para compartir. Cada una tiene sus propias condiciones y políticas de privacidad; no somos responsables de su contenido.</p>

        <h2>Más información</h2>
        <p>Cómo tratamos tus datos: <a href="{{ route('legal.privacidad') }}">política de privacidad</a>. Qué guardamos en tu navegador: <a href="{{ route('legal.cookies') }}">política de cookies</a>. Normas para usar la guía y publicar reseñas: <a href="{{ route('legal.condiciones') }}">condiciones de uso</a>.</p>

        <h2>Ley aplicable</h2>
        <p>Este aviso se rige por la ley española. Si eres consumidor, para cualquier conflicto puedes acudir a los juzgados de tu domicilio.</p>
    </x-pagina-legal>
@endcomponent
