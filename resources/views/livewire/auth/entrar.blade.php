<div class="mx-auto max-w-md px-4 py-10">
    <h1 class="text-3xl font-extrabold">Entrar</h1>
    <p class="mt-1 text-gris">¿Aún no tienes cuenta? <a href="{{ route('registro') }}" wire:navigate class="font-bold text-pino underline">Créala gratis</a>.</p>

    <form wire:submit="entrar" class="tarjeta mt-6 grid gap-4 p-5">
        <div>
            <label for="email" class="etiqueta">Email</label>
            <input id="email" type="email" wire:model="email" autocomplete="email" required autofocus class="campo">
            @error('email') <p class="error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="password" class="etiqueta">Contraseña</label>
            <input id="password" type="password" wire:model="password" autocomplete="current-password" required class="campo">
            @error('password') <p class="error">{{ $message }}</p> @enderror
        </div>
        <label class="flex items-center gap-2">
            <input type="checkbox" wire:model="recordar" class="size-5 accent-pino"> Mantener la sesión abierta
        </label>
        <button class="btn btn-pri" wire:loading.attr="disabled">Entrar</button>
    </form>
</div>
