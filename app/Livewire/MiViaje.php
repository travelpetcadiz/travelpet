<?php

namespace App\Livewire;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Mi viaje')]
class MiViaje extends Component
{
    public function quitar(int $sitioId): void
    {
        auth()->user()->viaje()->detach($sitioId);
    }

    public function render()
    {
        $sitios = auth()->user()->viaje()->with('municipio:id,nombre')->orderBy('nombre')->get();

        return view('livewire.mi-viaje', [
            'quiero' => $sitios->where('pivot.estado', 'quiero'),
            'visitados' => $sitios->where('pivot.estado', 'visitado'),
        ]);
    }
}
