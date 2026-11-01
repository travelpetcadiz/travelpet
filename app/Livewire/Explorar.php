<?php

namespace App\Livewire;

use App\Models\Municipio;
use App\Models\Provincia;
use App\Models\Sitio;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\Livewire;
use Livewire\WithPagination;

class Explorar extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public string $q = '';

    #[Url(except: '')]
    public string $tipo = '';

    #[Url(except: '')]
    public string $provincia = '';

    #[Url(except: '')]
    public string $comarca = '';

    #[Url(except: '')]
    public string $municipio = '';

    #[Url(except: '')]
    public string $admite = '';

    #[Url(except: false)]
    public bool $verificados = false;

    #[Url(except: 'lista')]
    public string $vista = 'lista';

    public function updated(string $propiedad): void
    {
        if ($propiedad !== 'vista') {
            $this->resetPage();
        }
        if ($propiedad === 'provincia') {
            $this->reset('comarca', 'municipio');
        }
        if ($propiedad === 'comarca') {
            $this->municipio = '';
        }
    }

    public function limpiar(): void
    {
        $this->reset('q', 'tipo', 'provincia', 'comarca', 'municipio', 'admite', 'verificados');
        $this->resetPage();
    }

    // Municipios que cumplen los filtros geográficos (provincia y comarca)
    private function municipios(): Builder
    {
        return Municipio::query()
            ->whereHas('provincia', fn (Builder $p) => $p->where('activa', true))
            ->when($this->provincia !== '', fn (Builder $q) => $q->whereRelation('provincia', 'slug', $this->provincia))
            ->when($this->comarca !== '', fn (Builder $q) => $q->where('comarca', $this->comarca));
    }

    private function consulta(): Builder
    {
        return Sitio::publicados()
            ->when(trim($this->q) !== '', function (Builder $q) {
                $t = '%'.trim($this->q).'%';
                $q->where(fn (Builder $w) => $w->where('nombre', 'like', $t)
                    ->orWhere('subtipo', 'like', $t)
                    ->orWhereRelation('municipio', 'nombre', 'like', $t));
            })
            ->when($this->tipo !== '', fn (Builder $q) => $q->where('tipo', $this->tipo))
            ->when($this->provincia !== '' || $this->comarca !== '', fn (Builder $q) => $q->whereIn('municipio_id', $this->municipios()->select('id')))
            ->when($this->municipio !== '', fn (Builder $q) => $q->where('municipio_id', (int) $this->municipio))
            ->when($this->admite !== '', fn (Builder $q) => $q->where('admite_perros', $this->admite))
            ->when($this->verificados, fn (Builder $q) => $q->where('verificado', true));
    }

    public function render()
    {
        // Los puntos solo viajan al navegador cuando se está viendo el mapa
        $puntos = $this->vista !== 'mapa' ? collect() : $this->consulta()
            ->whereNotNull('latitud')
            ->with('municipio:id,nombre')
            ->get(['municipio_id', 'nombre', 'slug', 'tipo', 'latitud', 'longitud', 'admite_perros', 'verificado'])
            ->map(fn (Sitio $s) => [
                'nombre' => $s->nombre,
                'url' => route('sitio', $s),
                'tipo' => $s->etiquetaTipo(),
                'color' => $s->color(),
                'municipio' => $s->municipio->nombre,
                'admite' => $s->etiquetaAdmite(),
                'verificado' => $s->verificado,
                'lat' => $s->latitud,
                'lng' => $s->longitud,
            ]);

        // En la carga inicial los puntos van en el propio HTML; después, por evento
        if ($this->vista === 'mapa' && Livewire::isLivewireRequest()) {
            $this->dispatch('puntos', puntos: $puntos);
        }

        $provincias = Provincia::activas()->orderBy('nombre')->get(['id', 'nombre', 'slug']);
        // Con una sola provincia activa no hace falta elegirla
        $unaSola = $provincias->count() === 1;
        $geoElegida = $unaSola || $this->provincia !== '';

        return view('livewire.explorar', [
            'sitios' => $this->consulta()->with('municipio:id,nombre')->orderByDesc('verificado')->orderBy('nombre')->paginate(24),
            'puntos' => $puntos,
            'provincias' => $provincias,
            'unaSola' => $unaSola,
            'comarcas' => $geoElegida
                ? Municipio::query()->whereHas('provincia', fn (Builder $p) => $p->where('activa', true))
                    ->when($this->provincia !== '', fn (Builder $q) => $q->whereRelation('provincia', 'slug', $this->provincia))
                    ->whereNotNull('comarca')->distinct()->orderBy('comarca')->pluck('comarca')
                : collect(),
            'municipios' => $geoElegida ? $this->municipios()->has('sitios')->orderBy('nombre')->pluck('nombre', 'id') : collect(),
            'conteoTipos' => Sitio::publicados()->selectRaw('tipo, count(*) n')->groupBy('tipo')->pluck('n', 'tipo'),
            'mapaCfg' => mapa_config(),
        ]);
    }
}
