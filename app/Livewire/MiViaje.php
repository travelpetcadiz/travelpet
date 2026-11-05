<?php

namespace App\Livewire;

use App\Models\Sitio;
use Illuminate\Support\Collection;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Mi viaje')]
class MiViaje extends Component
{
    /**
     * Sin cuenta, lo guardado vive en el navegador: la página lo manda aquí al cargar.
     *
     * @var array<int|string, string>
     */
    public array $local = [];

    /** Sin cuenta, hasta que el navegador manda lo guardado no se sabe si está vacío */
    public bool $cargado = false;

    public function mount(): void
    {
        $this->cargado = auth()->check();
    }

    /**
     * @param  array<int|string, string>  $items
     */
    public function cargarLocal(array $items): void
    {
        $this->local = array_filter($items, fn ($estado) => in_array($estado, ['quiero', 'visitado'], true));
        $this->cargado = true;
    }

    public function render()
    {
        $sitios = $this->sitios();

        return view('livewire.mi-viaje', [
            'quiero' => $sitios->where('estado_viaje', 'quiero'),
            'visitados' => $sitios->where('estado_viaje', 'visitado'),
        ]);
    }

    /**
     * Los sitios guardados, cada uno con su estado en «estado_viaje».
     *
     * @return Collection<int, Sitio>
     */
    private function sitios(): Collection
    {
        if ($user = auth()->user()) {
            return $user->viaje()->with('municipio:id,nombre')->orderBy('nombre')->get()
                ->each(fn (Sitio $s) => $s->setAttribute('estado_viaje', $s->pivot->estado));
        }

        $ids = collect(array_keys($this->local))->filter(fn ($id) => ctype_digit((string) $id))->take(200);

        return Sitio::publicados()->whereKey($ids)->with('municipio:id,nombre')->orderBy('nombre')->get()
            ->each(fn (Sitio $s) => $s->setAttribute('estado_viaje', $this->local[$s->id]));
    }
}
