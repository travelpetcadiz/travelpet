@php($t = config('travelpet.titular'))
@component('layouts.app', ['title' => 'Aviso legal'])
    <div class="prosa mx-auto max-w-3xl px-4 py-8">
        <h1 class="mb-4 text-3xl font-extrabold">Aviso legal</h1>
        <p>En cumplimiento de la Ley 34/2002, de servicios de la sociedad de la información y de comercio electrónico (LSSI), se informa de los datos del titular de este sitio web:</p>
        <ul class="mb-3 list-disc pl-6">
            <li>Titular: {{ $t['nombre'] ?: '[PENDIENTE: nombre y apellidos o razón social]' }}</li>
            <li>NIF: {{ $t['nif'] ?: '[PENDIENTE]' }}</li>
            <li>Domicilio: {{ $t['domicilio'] ?: '[PENDIENTE]' }}</li>
            <li>Email: <a href="mailto:{{ $t['email'] }}">{{ $t['email'] }}</a></li>
        </ul>
        <h2>Contenido de la guía</h2>
        <p>TravelPet Cádiz es una guía gratuita e informativa. Cada ficha indica de dónde sale el dato y cuándo se comprobó, pero las condiciones de los establecimientos pueden cambiar. Recomendamos confirmarlas con el sitio antes de ir. Las reseñas reflejan la opinión de sus autores.</p>
        <h2>Fotos</h2>
        <p>Las fotos son de sus autores, que se indican debajo de cada una. Algunas son del equipo de TravelPet, otras nos las han cedido los propios sitios y otras tienen licencia libre (Wikimedia Commons), con su autor y su licencia enlazados. Si eres autor de una foto, sales en ella y quieres que la retiremos, escríbenos a <a href="mailto:{{ $t['email'] }}">{{ $t['email'] }}</a> y la quitaremos.</p>
        <h2>Enlaces</h2>
        <p>Los enlaces a webs de terceros se ofrecen solo como referencia; no somos responsables de su contenido.</p>
    </div>
@endcomponent
