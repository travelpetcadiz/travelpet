<?php

namespace App\Livewire;

use App\Models\Foto;
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

    // Filtros prácticos. Solo muestran lo que sabemos con seguridad
    #[Url(except: false)]
    public bool $grandes = false;

    #[Url(except: false)]
    public bool $gratis = false;

    #[Url(except: false)]
    public bool $dentro = false;

    #[Url(except: false)]
    public bool $suelto = false;

    #[Url(except: false)]
    public bool $hoy = false;

    #[Url(except: false)]
    public bool $urgencias = false;

    // Ubicación de quien busca («Cerca de mí»). No va en la URL ni se guarda
    public ?float $lat = null;

    public ?float $lng = null;

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
        $this->reset('q', 'tipo', 'provincia', 'comarca', 'municipio', 'admite', 'verificados', 'grandes', 'gratis', 'dentro', 'suelto', 'hoy', 'urgencias');
        $this->resetPage();
    }

    // Buscador de la portada: busca al enviar, no mientras se escribe (si no, la portada desaparecería a mitad de palabra)
    public function buscar(): void
    {
        $this->resetPage();
    }

    public function cerca(float $lat, float $lng): void
    {
        if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
            return;
        }
        [$this->lat, $this->lng] = [$lat, $lng];
        $this->resetPage();
    }

    public function sinUbicacion(): void
    {
        $this->reset('lat', 'lng');
        $this->resetPage();
    }

    // Kilómetros aproximados hasta quien busca (suficiente para ordenar dentro de una provincia)
    private function distancia(): string
    {
        return '(SQRT(POW(latitud - ?, 2) + POW((longitud - ?) * '.cos(deg2rad($this->lat)).', 2)) * 111.32)';
    }

    // Sitios con periodo (playas) a los que hoy no se puede ir
    private function cerradosHoy(): array
    {
        return Sitio::whereNotNull('periodo_tipo')->get()
            ->filter(fn (Sitio $s) => ($s->hoy()['puede'] ?? true) === false)
            ->modelKeys();
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
            ->when($this->urgencias, fn (Builder $q) => $q->where('urgencias_24h', true))
            ->when($this->grandes, fn (Builder $q) => $q->where('admite_grandes', true))
            ->when($this->gratis, fn (Builder $q) => $q->where('sin_suplemento', true))
            ->when($this->dentro, fn (Builder $q) => $q->where('puede_entrar', true))
            ->when($this->suelto, fn (Builder $q) => $q->where('sin_correa', true))
            ->when($this->hoy, fn (Builder $q) => $q->whereNotNull('periodo_tipo')->whereKeyNot($this->cerradosHoy()))
            ->when($this->tipo !== '', fn (Builder $q) => $q->where('tipo', $this->tipo))
            ->when($this->provincia !== '' || $this->comarca !== '', fn (Builder $q) => $q->whereIn('municipio_id', $this->municipios()->select('id')))
            ->when($this->municipio !== '', fn (Builder $q) => $q->where('municipio_id', (int) $this->municipio))
            ->when($this->admite !== '', fn (Builder $q) => $q->where('admite_perros', $this->admite))
            ->when($this->verificados, fn (Builder $q) => $q->where('verificado', true));
    }

    // Con ubicación, del más cercano al más lejano; sin ella, primero lo confirmado
    private function ordenados(): Builder
    {
        // La primera foto de cada sitio sale en su tarjeta
        $q = $this->consulta()->with(['municipio:id,nombre', 'fotos' => fn ($f) => $f->publicadas()]);
        if ($this->lat === null) {
            return $q->orderByDesc('verificado')->orderBy('nombre');
        }

        return $q->select('sitios.*')
            ->selectRaw($this->distancia().' AS distancia_km', [$this->lat, $this->lng])
            ->orderByRaw('latitud IS NULL')
            ->orderBy('distancia_km');
    }

    public function render()
    {
        // Los puntos solo viajan al navegador cuando se está viendo el mapa
        $puntos = $this->vista !== 'mapa' ? collect() : $this->consulta()
            ->whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->where('a_domicilio', false)
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
            'sitios' => $this->ordenados()->paginate(24),
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
            // Fotos del carrusel de la portada: las que Natalia marca «para la portada» en el panel
            'fotosPortada' => Foto::paraPortada()->get(),
        ]);
    }

    /**
     * Paginación propia: flechas y números de página, con los textos en español.
     */
    public function paginationView(): string
    {
        return 'livewire.partes.paginacion';
    }
}
