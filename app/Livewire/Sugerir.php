<?php

namespace App\Livewire;

use App\Models\Municipio;
use App\Models\Provincia;
use App\Models\Sugerencia;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Sugerir un sitio')]
class Sugerir extends Component
{
    public string $nombre = '';

    public string $municipio = '';

    public string $provincia_id = '';

    public string $mensaje = '';

    public bool $enviado = false;

    public function enviar(): void
    {
        $clave = 'sugerencia:'.auth()->id();
        abort_if(RateLimiter::tooManyAttempts($clave, 10), 429, 'Demasiados envíos. Prueba dentro de un rato.');
        abort_unless(auth()->user()->activo, 403);

        $this->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'municipio' => ['required', 'string', 'max:255'],
            'provincia_id' => ['nullable', 'exists:provincias,id'],
            'mensaje' => ['required', 'string', 'min:10', 'max:2000'],
        ], [], ['nombre' => 'nombre del sitio', 'mensaje' => 'información']);

        RateLimiter::hit($clave, 3600);
        Sugerencia::create([
            'user_id' => auth()->id(),
            'tipo' => 'nuevo_sitio',
            'nombre' => trim($this->nombre),
            'municipio' => trim($this->municipio),
            'provincia_id' => $this->provincia_id ?: null,
            'mensaje' => trim($this->mensaje),
        ]);

        $this->reset('nombre', 'municipio', 'provincia_id', 'mensaje');
        $this->enviado = true;
    }

    public function render()
    {
        return view('livewire.sugerir', [
            'provincias' => Provincia::orderBy('nombre')->pluck('nombre', 'id'),
            'municipios' => Municipio::query()
                ->when($this->provincia_id !== '', fn ($q) => $q->where('provincia_id', $this->provincia_id))
                ->orderBy('nombre')->pluck('nombre'),
        ]);
    }
}
