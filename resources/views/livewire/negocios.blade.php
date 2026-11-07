@php
    use App\Models\Sitio;
    $paso = fn (int $n, string $texto) => '<li class="flex gap-3"><span class="grid size-8 flex-none place-items-center rounded-full bg-pino font-bold text-white">'.$n.'</span><span class="pt-1">'.$texto.'</span></li>';
    $sello = '<span class="sello bg-ok-suave align-middle text-ok">'.svg('heroicon-s-check-badge', 'size-4 flex-none', ['aria-hidden' => 'true'])->toHtml().'Confirmado</span>';
@endphp
<div class="mx-auto grid max-w-xl gap-6 px-4 py-8">
    <header>
        <h1 class="text-3xl font-extrabold sm:text-4xl">¿Tu negocio admite mascotas?</h1>
        <p class="mt-2 text-lg text-gris">Cuéntanos tus condiciones y te añadimos a la guía. Aparecer es gratis.</p>
    </header>

    <ol class="grid gap-3">
        {!! $paso(1, 'Rellenas este formulario con tus condiciones.') !!}
        {!! $paso(2, 'Lo revisamos y lo publicamos con el sello '.$sello.'.') !!}
        {!! $paso(3, 'Si algo cambia, nos escribes y lo actualizamos.') !!}
    </ol>

    <p class="flex items-start gap-2.5 rounded-xl bg-pino-suave px-4 py-3">
        @svg('heroicon-s-check-badge', 'mt-0.5 size-5 flex-none text-pino', ['aria-hidden' => 'true'])
        <span>El sello «Confirmado» no se paga: solo depende de que nos confirmes tú los datos.</span>
    </p>

    @if ($enviado)
        <div class="rounded-xl bg-ok-suave p-4 text-ok" role="status">
            <p><strong>¡Gracias! Hemos recibido los datos de tu negocio.</strong> Los revisamos y te escribimos si tenemos alguna duda.</p>
            <button type="button" wire:click="$set('enviado', false)" class="mt-2 font-bold underline">Enviar otro negocio</button>
        </div>
    @else
        <form wire:submit="enviar" class="tarjeta grid gap-4 p-4 sm:p-5">
            <div>
                <label for="n-nombre" class="etiqueta">Nombre del negocio</label>
                <input id="n-nombre" type="text" wire:model="nombre" required maxlength="255" class="campo" placeholder="Por ejemplo, Bar Juanito">
                @error('nombre') <p class="error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="n-municipio" class="etiqueta">Pueblo</label>
                <input id="n-municipio" type="text" wire:model="municipio" required maxlength="255" class="campo" placeholder="Por ejemplo, Chiclana de la Frontera">
                @error('municipio') <p class="error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="n-tipo" class="etiqueta">Tipo de negocio</label>
                <select id="n-tipo" wire:model="tipo" required class="campo">
                    <option value="">Elige uno</option>
                    @foreach (Sitio::TIPOS as $clave => [$etiqueta])
                        <option value="{{ $clave }}">{{ $etiqueta }}</option>
                    @endforeach
                </select>
                @error('tipo') <p class="error">{{ $message }}</p> @enderror
            </div>
            <fieldset class="grid gap-2">
                <legend class="etiqueta">¿Admitís mascotas?</legend>
                @foreach (\App\Livewire\Negocios::ADMITE as $clave => $texto)
                    <label class="flex min-h-11 items-center gap-2 rounded-xl border px-3 font-bold {{ $admite === $clave ? 'border-pino bg-pino-suave' : 'border-linea bg-white' }}">
                        <input type="radio" wire:model.live="admite" value="{{ $clave }}" class="size-5 accent-pino">{{ $texto }}
                    </label>
                @endforeach
                @error('admite') <p class="error">{{ $message }}</p> @enderror
            </fieldset>
            <div>
                <label for="n-condiciones" class="etiqueta">Condiciones</label>
                <textarea id="n-condiciones" wire:model="condiciones" rows="4" maxlength="2000" class="campo" placeholder="Terraza o interior, tamaño, suplemento, si hay que avisar al reservar…"></textarea>
                @error('condiciones') <p class="error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="n-contacto" class="etiqueta">Correo o teléfono de contacto</label>
                <input id="n-contacto" type="text" wire:model="contacto" required maxlength="255" autocomplete="email" class="campo">
                <p class="mt-1 text-sm text-gris">Solo para dudas sobre tu ficha. No se publica.</p>
                @error('contacto') <p class="error">{{ $message }}</p> @enderror
            </div>
            <div class="hidden" aria-hidden="true">
                <label for="n-web">No rellenes este campo</label>
                <input id="n-web" type="text" wire:model="web" tabindex="-1" autocomplete="off">
            </div>
            <div>
                <label class="flex items-start gap-2">
                    <input type="checkbox" wire:model="trabajaAqui" class="mt-0.5 size-5 flex-none accent-pino">
                    Trabajo en este negocio y los datos son correctos.
                </label>
                @error('trabajaAqui') <p class="error">{{ $message }}</p> @enderror
            </div>
            <p class="text-sm text-gris">Usamos tu correo o teléfono solo para comprobar estos datos contigo: no se publica ni se lo damos a nadie, y se borra como máximo 12 meses después de revisar tu solicitud. Más información en la <a href="{{ route('legal.privacidad') }}" class="underline">política de privacidad</a>.</p>
            <button class="btn btn-pri" wire:loading.attr="disabled">@svg('heroicon-o-paper-airplane', 'size-5', ['aria-hidden' => 'true'])Enviar mis datos</button>
        </form>
    @endif
</div>
