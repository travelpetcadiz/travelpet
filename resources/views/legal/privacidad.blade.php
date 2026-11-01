@php($t = config('travelpet.titular'))
@component('layouts.app', ['title' => 'Política de privacidad'])
    <div class="prosa mx-auto max-w-3xl px-4 py-8">
        <h1 class="mb-4 text-3xl font-extrabold">Política de privacidad</h1>
        <p><strong>Responsable:</strong> {{ $t['nombre'] ?: '[PENDIENTE: titular]' }} · <a href="mailto:{{ $t['email'] }}">{{ $t['email'] }}</a></p>
        <h2>Qué datos tratamos y para qué</h2>
        <p>Si creas una cuenta guardamos tu nombre, tu email y tu contraseña cifrada para que puedas entrar, publicar reseñas, avisar de datos incorrectos, sugerir sitios y guardar tu viaje. Tu nombre aparece junto a las reseñas que se publiquen; tu email nunca se muestra.</p>
        <h2>Base legal</h2>
        <p>Tu consentimiento al crear la cuenta (art. 6.1.a del RGPD). Puedes retirarlo en cualquier momento pidiendo que borremos tu cuenta.</p>
        <h2>Cuánto tiempo</h2>
        <p>Mientras mantengas la cuenta. Si nos pides borrarla, eliminamos tus datos y tus reseñas.</p>
        <h2>Con quién se comparten</h2>
        <p>Con nadie. Los datos se guardan en nuestro propio servidor. No usamos cookies de publicidad ni de analítica: solo la cookie técnica de sesión necesaria para entrar.</p>
        <h2>Tus derechos</h2>
        <p>Puedes pedir acceso, rectificación, supresión, oposición, limitación y portabilidad escribiendo a <a href="mailto:{{ $t['email'] }}">{{ $t['email'] }}</a>. Si no quedas conforme, puedes reclamar ante la Agencia Española de Protección de Datos (aepd.es).</p>
    </div>
@endcomponent
