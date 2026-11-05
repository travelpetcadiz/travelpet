<?php

namespace App\Livewire;

use App\Models\Sitio;
use App\Models\Sugerencia;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * «¿Tu negocio admite mascotas?»: un negocio manda sus condiciones para aparecer en la guía.
 * No hace falta cuenta. Llega al panel como sugerencia de tipo «negocio» y se revisa antes de publicar.
 */
#[Title('¿Tu negocio admite mascotas?')]
class Negocios extends Component
{
    public const ADMITE = [
        'si' => 'Sí',
        'condiciones' => 'Sí, con condiciones',
        'no' => 'No',
    ];

    public string $nombre = '';

    public string $municipio = '';

    public string $tipo = '';

    public string $admite = '';

    public string $condiciones = '';

    public string $contacto = '';

    public bool $trabajaAqui = false;

    /** Campo trampa para robots: una persona no lo ve ni lo rellena */
    public string $web = '';

    public bool $enviado = false;

    public function enviar(): void
    {
        $clave = 'negocio:'.request()->ip();
        abort_if(RateLimiter::tooManyAttempts($clave, 5), 429, 'Demasiados envíos. Prueba dentro de un rato.');

        $this->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'municipio' => ['required', 'string', 'max:255'],
            'tipo' => ['required', 'in:'.implode(',', array_keys(Sitio::TIPOS))],
            'admite' => ['required', 'in:'.implode(',', array_keys(self::ADMITE))],
            'condiciones' => ['nullable', 'string', 'max:2000'],
            'contacto' => ['required', 'string', 'max:255'],
            'trabajaAqui' => ['accepted'],
        ], [
            'trabajaAqui.accepted' => 'Marca la casilla para confirmar que trabajas en este negocio.',
        ], [
            'nombre' => 'nombre del negocio',
            'municipio' => 'pueblo',
            'tipo' => 'tipo de negocio',
            'admite' => 'si admitís mascotas',
            'contacto' => 'correo o teléfono',
        ]);

        RateLimiter::hit($clave, 3600);

        if ($this->web === '') {
            Sugerencia::create([
                'user_id' => auth()->id(),
                'tipo' => 'negocio',
                'nombre' => trim($this->nombre),
                'municipio' => trim($this->municipio),
                'mensaje' => implode("\n", array_filter([
                    'Tipo: '.Sitio::TIPOS[$this->tipo][0],
                    '¿Admiten mascotas?: '.self::ADMITE[$this->admite],
                    trim($this->condiciones) !== '' ? 'Condiciones: '.trim($this->condiciones) : null,
                    'Contacto (no se publica): '.trim($this->contacto),
                    'Confirma que trabaja en el negocio.',
                ])),
            ]);
        }

        $this->reset('nombre', 'municipio', 'tipo', 'admite', 'condiciones', 'contacto', 'trabajaAqui');
        $this->enviado = true;
    }

    public function render()
    {
        return view('livewire.negocios');
    }
}
