<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Entrar')]
class Entrar extends Component
{
    public string $email = '';

    public string $password = '';

    public bool $recordar = true;

    public function entrar()
    {
        $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $clave = 'entrar:'.Str::lower($this->email).'|'.request()->ip();
        if (RateLimiter::tooManyAttempts($clave, 5)) {
            throw ValidationException::withMessages([
                'email' => 'Demasiados intentos. Prueba otra vez en '.RateLimiter::availableIn($clave).' segundos.',
            ]);
        }

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password, 'activo' => true], $this->recordar)) {
            RateLimiter::hit($clave, 60);
            throw ValidationException::withMessages(['email' => 'El email o la contraseña no son correctos.']);
        }

        RateLimiter::clear($clave);
        session()->regenerate();

        // El equipo va directo al panel
        return Auth::user()->isStaff()
            ? redirect()->intended('/admin')
            : $this->redirectIntended(route('inicio'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.entrar');
    }
}
