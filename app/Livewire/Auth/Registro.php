<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Crear cuenta')]
class Registro extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public bool $acepto = false;

    // Campo trampa contra bots: las personas no lo ven
    public string $web = '';

    public function registrar()
    {
        $clave = 'registro:'.request()->ip();
        if (RateLimiter::tooManyAttempts($clave, 5) || $this->web !== '') {
            throw ValidationException::withMessages(['email' => 'No se ha podido crear la cuenta. Prueba más tarde.']);
        }

        $datos = $this->validate([
            'name' => ['required', 'string', 'max:40'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'acepto' => ['accepted'],
        ], [
            'acepto.accepted' => 'Para crear la cuenta tienes que tener 14 años o más y aceptar las condiciones de uso y la política de privacidad.',
            'email.unique' => 'Ya hay una cuenta con este email. ¿Quieres entrar?',
        ], ['name' => 'nombre']);

        RateLimiter::hit($clave, 3600);

        $user = User::create([
            'name' => $datos['name'],
            'email' => $datos['email'],
            'password' => $datos['password'],
            'role' => 'usuario',
        ]);

        Auth::login($user, true);
        session()->regenerate();

        return $this->redirectIntended(route('inicio'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.registro');
    }
}
