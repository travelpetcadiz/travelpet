@php($t = config('travelpet.titular'))
@component('layouts.app', ['title' => 'Política de privacidad', 'descripcion' => 'Qué datos tratamos en TravelPet, para qué, durante cuánto tiempo y cómo puedes ejercer tus derechos.'])
    <x-pagina-legal titulo="Política de privacidad">
        <p><strong>En resumen:</strong> para usar la guía no necesitamos ningún dato tuyo. Solo tratamos datos si creas una cuenta, publicas algo o nos escribes, y únicamente para eso. No vendemos ni cedemos datos, no hay publicidad y no usamos herramientas de analítica de terceros.</p>

        <h2>Quién es el responsable</h2>
        <ul>
            <li><strong>Responsable:</strong> {{ $t['nombre'] }}{{ $t['nif'] ? ' · NIF '.$t['nif'] : '' }}</li>
            @if ($t['domicilio'])
                <li><strong>Domicilio:</strong> {{ $t['domicilio'] }}</li>
            @endif
            <li><strong>Correo para cualquier asunto de privacidad:</strong> <a href="mailto:{{ $t['email'] }}">{{ $t['email'] }}</a></li>
        </ul>

        <h2>Qué datos tratamos, para qué y durante cuánto tiempo</h2>
        <div class="overflow-x-auto"><table>
            <thead>
                <tr><th>Cuándo</th><th>Qué datos</th><th>Para qué y con qué base legal</th><th>Cuánto tiempo</th></tr>
            </thead>
            <tbody>
                <tr>
                    <td>Si creas una cuenta</td>
                    <td>Nombre, correo y contraseña (guardada cifrada: nadie puede leerla).</td>
                    <td>Que puedas entrar y usar las funciones con cuenta. Base: las condiciones de uso que aceptas al registrarte (art. 6.1.b RGPD).</td>
                    <td>Mientras tengas la cuenta. Si nos pides borrarla, la borramos junto con tus reseñas y tu viaje.</td>
                </tr>
                <tr>
                    <td>Si escribes una reseña</td>
                    <td>Texto, puntuación, si pudiste entrar y el tamaño de tu mascota. <strong>Tu nombre de la cuenta se publica junto a la reseña</strong>; tu correo, nunca.</td>
                    <td>Publicarla en la ficha del sitio después de revisarla. Base: art. 6.1.b RGPD.</td>
                    <td>Mientras esté publicada o hasta que borres tu cuenta.</td>
                </tr>
                <tr>
                    <td>Si sugieres un sitio o avisas de un dato mal</td>
                    <td>Lo que nos escribes y tu cuenta.</td>
                    <td>Revisar la propuesta y mejorar la guía. Base: interés legítimo en que la información sea correcta (art. 6.1.f RGPD).</td>
                    <td>Hasta que la revisamos. Si borras tu cuenta, la sugerencia se queda sin tu nombre.</td>
                </tr>
                <tr>
                    <td>Si envías los datos de tu negocio</td>
                    <td>Datos del negocio y un correo o teléfono de contacto. El contacto <strong>no se publica</strong>.</td>
                    <td>Comprobar contigo la información antes de publicarla. Base: tu solicitud (art. 6.1.b RGPD).</td>
                    <td>El contacto se borra solo como máximo 12 meses después de revisar la solicitud.</td>
                </tr>
                <tr>
                    <td>Si usas «Mi viaje» sin cuenta</td>
                    <td>Los sitios que guardas.</td>
                    <td>Recordarlos. <strong>Se guardan solo en tu navegador</strong>; no nos llegan.</td>
                    <td>Hasta que los quites o borres los datos del navegador.</td>
                </tr>
                <tr>
                    <td>Si pulsas «Cerca de mí»</td>
                    <td>Tu ubicación aproximada, solo si das permiso en el navegador.</td>
                    <td>Ordenar los sitios del más cercano al más lejano. Base: tu consentimiento (art. 6.1.a RGPD), que das al aceptar el aviso del navegador.</td>
                    <td><strong>No se guarda</strong>: se usa en esa búsqueda y se olvida.</td>
                </tr>
                <tr>
                    <td>Siempre que visitas la web</td>
                    <td>Registros técnicos del servidor: dirección IP, fecha y página pedida.</td>
                    <td>Seguridad, evitar abusos y corregir errores. Base: interés legítimo (art. 6.1.f RGPD).</td>
                    <td>Se borran solos a los 14 días.</td>
                </tr>
            </tbody>
        </table></div>
        <p>Además, hacemos copias de seguridad de la base de datos para no perder la información; se borran solas a los pocos días (como máximo, una semana).</p>
        <p>No tomamos decisiones automatizadas sobre ti ni hacemos perfiles.</p>

        <h2>Con quién se comparten</h2>
        <p>No cedemos tus datos a nadie. Solo intervienen estos proveedores, que nos dan servicio y no pueden usar los datos para nada más:</p>
        <ul>
            <li><strong>Contabo GmbH</strong>: el servidor donde está la web y la base de datos, en un centro de datos de Francia (Unión Europea).</li>
            <li><strong>OpenFreeMap</strong>: dibuja el mapa. Cuando lo abres, tu navegador le pide las imágenes del mapa, así que recibe tu dirección IP y la zona que estás viendo. No usa cookies. Más información en <a href="https://openfreemap.org" rel="noopener">openfreemap.org</a>.</li>
        </ul>
        <p>Si pulsas «Cómo llegar» o «Compartir», pasas a Google Maps o a WhatsApp, que tratan tus datos según sus propias políticas.</p>
        <p>No hacemos transferencias internacionales de datos: todo se guarda en la Unión Europea.</p>

        <h2>Edad mínima</h2>
        <p>Para crear una cuenta hay que tener al menos 14 años, como pide la ley española (art. 7 LOPDGDD). Si tienes menos, pide a tu madre, padre o tutor que la cree.</p>

        <h2>Tus derechos</h2>
        <p>Puedes pedirnos en cualquier momento que te digamos qué datos tenemos tuyos, que los corrijamos, que los borremos (incluida tu cuenta), que dejemos de usarlos o que te los demos en un archivo para llevártelos. También puedes retirar tu consentimiento cuando quieras.</p>
        <p>Escríbenos a <a href="mailto:{{ $t['email'] }}">{{ $t['email'] }}</a> desde el correo de tu cuenta, diciendo qué necesitas. Te contestamos en un mes como máximo.</p>
        <p>Si crees que no hemos tratado bien tus datos, puedes reclamar ante la Agencia Española de Protección de Datos en <a href="https://www.aepd.es" rel="noopener">aepd.es</a>.</p>

        <h2>Cómo los protegemos</h2>
        <ul>
            <li>Toda la web va cifrada (https).</li>
            <li>Las contraseñas se guardan cifradas.</li>
            <li>Solo el equipo de TravelPet, con su propia cuenta, puede entrar al panel de gestión.</li>
            <li>A las fotos que se suben se les quita la ubicación GPS y el resto de datos ocultos.</li>
        </ul>

        <h2>Cambios</h2>
        <p>Si cambiamos esta política, lo diremos aquí con la fecha de actualización. Si el cambio es importante, lo avisaremos también en la portada de la web.</p>
    </x-pagina-legal>
@endcomponent
