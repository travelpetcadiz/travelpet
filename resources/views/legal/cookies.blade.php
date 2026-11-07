@php($t = config('travelpet.titular'))
@component('layouts.app', ['title' => 'Política de cookies', 'descripcion' => 'Qué guarda TravelPet en tu navegador: solo lo imprescindible para que la web funcione. Sin publicidad ni analítica.'])
    <x-pagina-legal titulo="Política de cookies">
        <p><strong>En resumen:</strong> TravelPet solo usa lo imprescindible para funcionar. No hay cookies de publicidad, ni de analítica, ni de redes sociales. Por eso no te mostramos el aviso de «aceptar cookies»: las que usamos están exentas de consentimiento según el artículo 22.2 de la LSSI.</p>

        <h2>Qué es una cookie</h2>
        <p>Un pequeño archivo que la web guarda en tu navegador para recordar algo entre una página y otra, por ejemplo que has entrado con tu cuenta.</p>

        <h2>Las que usamos</h2>
        <div class="overflow-x-auto"><table>
            <thead>
                <tr><th>Nombre</th><th>Para qué</th><th>Cuánto dura</th></tr>
            </thead>
            <tbody>
                <tr>
                    <td><code>travelpet-session</code></td>
                    <td>Mantener tu visita: que la web sepa si has entrado con tu cuenta y recuerde lo que haces mientras navegas.</td>
                    <td>2 horas sin actividad</td>
                </tr>
                <tr>
                    <td><code>XSRF-TOKEN</code></td>
                    <td>Seguridad: comprueba que los formularios los envías tú desde nuestra web y no otra web haciéndose pasar por ti.</td>
                    <td>2 horas</td>
                </tr>
                <tr>
                    <td><code>remember_web_…</code></td>
                    <td>Solo si entras con «Mantener la sesión abierta» (o al crear tu cuenta): para no pedirte la contraseña cada vez.</td>
                    <td>Hasta que pulses «Salir», como máximo 400 días</td>
                </tr>
            </tbody>
        </table></div>

        <h2>Lo que guardamos en tu navegador sin ser cookies</h2>
        <div class="overflow-x-auto"><table>
            <thead>
                <tr><th>Nombre</th><th>Para qué</th><th>Cuánto dura</th></tr>
            </thead>
            <tbody>
                <tr>
                    <td><code>travelpet-viaje</code></td>
                    <td>Los sitios que guardas en «Mi viaje» sin tener cuenta. Se quedan en tu navegador: no nos llegan.</td>
                    <td>Hasta que los quites o borres los datos del navegador</td>
                </tr>
            </tbody>
        </table></div>

        <h2>Servicios de otros</h2>
        <p>El mapa lo dibuja <strong>OpenFreeMap</strong>, que no usa cookies. Si pulsas «Cómo llegar» o «Compartir», pasas a Google Maps o a WhatsApp, que tienen sus propias políticas de cookies.</p>

        <h2>Cómo borrarlas</h2>
        <p>Puedes borrar las cookies y los datos guardados desde la configuración de tu navegador (normalmente en «Privacidad» o «Historial»). Si lo haces, se cerrará tu sesión y, si no tienes cuenta, se borrará tu «Mi viaje».</p>

        <p>¿Dudas? Escríbenos a <a href="mailto:{{ $t['email'] }}">{{ $t['email'] }}</a>. Más información en la <a href="{{ route('legal.privacidad') }}">política de privacidad</a>.</p>
    </x-pagina-legal>
@endcomponent
