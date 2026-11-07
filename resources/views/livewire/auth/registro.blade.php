<div class="mx-auto max-w-md px-4 py-10">
    <h1 class="text-3xl font-extrabold">Crear cuenta</h1>
    <p class="mt-1 text-gris">Para dejar reseñas, avisar de datos incorrectos y guardar tu viaje. ¿Ya tienes cuenta? <a href="{{ route('entrar') }}" wire:navigate class="font-bold text-pino underline">Entra</a>.</p>

    <form wire:submit="registrar" class="tarjeta mt-6 grid gap-4 p-5">
        <div>
            <label for="name" class="etiqueta">Nombre (se verá en tus reseñas)</label>
            <input id="name" type="text" wire:model="name" autocomplete="given-name" required maxlength="40" class="campo">
            @error('name') <p class="error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="email" class="etiqueta">Email</label>
            <input id="email" type="email" wire:model="email" autocomplete="email" required class="campo">
            @error('email') <p class="error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="password" class="etiqueta">Contraseña (mínimo 8 caracteres)</label>
            <input id="password" type="password" wire:model="password" autocomplete="new-password" required class="campo">
            @error('password') <p class="error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="password_confirmation" class="etiqueta">Repite la contraseña</label>
            <input id="password_confirmation" type="password" wire:model="password_confirmation" autocomplete="new-password" required class="campo">
        </div>
        <div class="hidden" aria-hidden="true">
            <label for="web">No rellenes este campo</label>
            <input id="web" type="text" wire:model="web" tabindex="-1" autocomplete="off">
        </div>
        <label class="flex items-start gap-2">
            <input type="checkbox" wire:model="acepto" class="mt-0.5 size-5 flex-none accent-pino">
            <span>Tengo 14 años o más y acepto las <a href="{{ route('legal.condiciones') }}" target="_blank" class="font-bold text-pino underline">condiciones de uso</a> y la <a href="{{ route('legal.privacidad') }}" target="_blank" class="font-bold text-pino underline">política de privacidad</a>.</span>
        </label>
        @error('acepto') <p class="error -mt-2">{{ $message }}</p> @enderror
        <button class="btn btn-pri" wire:loading.attr="disabled">Crear cuenta</button>
    </form>
</div>
