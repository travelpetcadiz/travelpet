<div class="mx-auto max-w-xl px-4 py-8">
    <h1 class="text-3xl font-extrabold">Sugerir un sitio</h1>
    <p class="mt-1 text-gris">¿Conoces un sitio que admite perros y no está en la guía? Cuéntanos lo que sepas y lo comprobaremos antes de publicarlo.</p>

    @if ($enviado)
        <p class="mt-6 rounded-xl bg-ok-suave p-4 text-ok" role="status">¡Gracias! Hemos recibido tu sugerencia. <button type="button" wire:click="$set('enviado', false)" class="font-bold underline">Sugerir otro</button></p>
    @else
        <form wire:submit="enviar" class="tarjeta mt-6 grid gap-4 p-5">
            <div>
                <label for="nombre" class="etiqueta">Nombre del sitio</label>
                <input id="nombre" type="text" wire:model="nombre" required class="campo">
                @error('nombre') <p class="error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="provincia" class="etiqueta">Provincia</label>
                <select id="provincia" wire:model.live="provincia_id" class="campo">
                    <option value="">Otra / no lo sé</option>
                    @foreach ($provincias as $id => $nombre)
                        <option value="{{ $id }}">{{ $nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="municipio" class="etiqueta">Municipio</label>
                <input id="municipio" type="text" wire:model="municipio" list="municipios" required class="campo">
                <datalist id="municipios">
                    @foreach ($municipios as $m)
                        <option value="{{ $m }}">
                    @endforeach
                </datalist>
                @error('municipio') <p class="error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="mensaje" class="etiqueta">¿Qué sabes de él?</label>
                <textarea id="mensaje" wire:model="mensaje" rows="5" class="campo" placeholder="Qué tipo de sitio es, si admite perros grandes, si hay que pagar suplemento, web o teléfono…"></textarea>
                @error('mensaje') <p class="error">{{ $message }}</p> @enderror
            </div>
            <button class="btn btn-pri justify-self-start" wire:loading.attr="disabled">Enviar sugerencia</button>
        </form>
    @endif
</div>
