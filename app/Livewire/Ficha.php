<?php

namespace App\Livewire;

use App\Models\Resena;
use App\Models\Sitio;
use App\Models\Sugerencia;
use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Ficha extends Component
{
    public Sitio $sitio;

    public int $estrellas = 5;

    public string $pudo_entrar = 'si';

    public string $tamano_perro = '';

    public string $texto = '';

    public string $error_mensaje = '';

    public bool $mostrarError = false;

    public function mount(Sitio $sitio): void
    {
        abort_unless($sitio->publicado || auth()->user()?->isStaff(), 404);
    }

    public function alternarViaje(string $estado): void
    {
        if (! $user = $this->usuario()) {
            return;
        }
        $actual = $user->viaje()->whereKey($this->sitio->id)->first()?->pivot->estado;

        if ($actual === $estado) {
            $user->viaje()->detach($this->sitio->id);
        } else {
            $user->viaje()->syncWithoutDetaching([$this->sitio->id => ['estado' => $estado]]);
        }
    }

    public function enviarResena(): void
    {
        if (! $user = $this->usuario()) {
            return;
        }
        $this->limitar('resena:'.$user->id);

        $this->validate([
            'estrellas' => ['required', 'integer', 'between:1,5'],
            'pudo_entrar' => ['required', Rule::in(['si', 'no'])],
            'tamano_perro' => ['nullable', Rule::in(array_keys(Resena::TAMANOS))],
            'texto' => ['required', 'string', 'min:20', 'max:1500'],
        ], [], ['texto' => 'reseña']);

        // Una reseña por persona y sitio: si ya tenía una, se sustituye y vuelve a revisión
        Resena::updateOrCreate(
            ['sitio_id' => $this->sitio->id, 'user_id' => $user->id],
            [
                'estrellas' => $this->estrellas,
                'pudo_entrar' => $this->pudo_entrar,
                'tamano_perro' => $this->tamano_perro ?: null,
                'texto' => trim($this->texto),
                'estado' => 'pendiente',
            ],
        );

        $this->reset('texto');
        session()->flash('resena', 'Gracias. Tu reseña se publicará cuando la revisemos.');
    }

    public function reportarError(): void
    {
        if (! $user = $this->usuario()) {
            return;
        }
        $this->limitar('sugerencia:'.$user->id);

        $this->validate(['error_mensaje' => ['required', 'string', 'min:10', 'max:2000']], [], ['error_mensaje' => 'mensaje']);

        Sugerencia::create([
            'user_id' => $user->id,
            'sitio_id' => $this->sitio->id,
            'tipo' => 'error',
            'mensaje' => trim($this->error_mensaje),
        ]);

        $this->reset('error_mensaje', 'mostrarError');
        session()->flash('error_enviado', 'Gracias por avisar. Lo revisaremos y corregiremos la ficha.');
    }

    // Sin sesión manda a entrar; las cuentas desactivadas no pueden publicar
    private function usuario(): ?User
    {
        $user = auth()->user();
        if (! $user) {
            $this->redirectRoute('entrar', navigate: true);

            return null;
        }
        abort_unless($user->activo, 403);

        return $user;
    }

    private function limitar(string $clave): void
    {
        abort_if(RateLimiter::tooManyAttempts($clave, 10), 429, 'Demasiados envíos. Prueba dentro de un rato.');
        RateLimiter::hit($clave, 3600);
    }

    // Tipo de schema.org de cada categoría (lo que entiende Google)
    private const TIPOS_SCHEMA = [
        'Alojamiento' => 'LodgingBusiness',
        'Restauración' => 'Restaurant',
        'Playa' => 'Beach',
        'Espacio' => 'Park',
        'Salud' => 'VeterinaryCare',
        'Servicios' => 'LocalBusiness',
        'Ocio' => 'TouristAttraction',
        'Transporte' => 'LocalBusiness',
        'Comercio' => 'Store',
        'Caravanas' => 'RVPark',
    ];

    /**
     * Datos estructurados (JSON-LD) para buscadores: tipo de sitio, dirección, contacto y si admite mascotas.
     * En alojamientos se usa petsAllowed; en el resto, amenityFeature, que es lo que admite schema.org.
     */
    private function datosEstructurados($resenas): array
    {
        $s = $this->sitio;
        $admite = match ($s->admite_perros) {
            'si' => true,
            'no' => false,
            'condiciones' => $s->esServicio() ? true : trim('Sí, con condiciones. '.implode('. ', array_filter([$s->tamano_maximo, $s->suplemento, $s->zonas_permitidas]))),
            default => null,
        };
        $esAlojamiento = $s->tipo === 'Alojamiento';
        $web = preg_match('#https?://[^\s;]+#', (string) $s->contacto, $w) ? $w[0] : null;

        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => self::TIPOS_SCHEMA[$s->tipo] ?? 'Place',
            'name' => $s->nombre,
            'url' => route('sitio', $s),
            'sameAs' => $web,
            'telephone' => $s->telefono(),
            'address' => array_filter([
                '@type' => 'PostalAddress',
                'streetAddress' => $s->direccion,
                'addressLocality' => $s->municipio->nombre,
                'addressRegion' => $s->municipio->provincia->nombre,
                'addressCountry' => $s->municipio->provincia->pais,
            ]),
            'image' => $s->fotos()->publicadas()->get()->map->url()->all() ?: null,
            'geo' => $s->latitud ? ['@type' => 'GeoCoordinates', 'latitude' => $s->latitud, 'longitude' => $s->longitud] : null,
            'petsAllowed' => $esAlojamiento ? $admite : null,
            'amenityFeature' => ! $esAlojamiento && $admite !== null
                ? [['@type' => 'LocationFeatureSpecification', 'name' => 'Admite mascotas', 'value' => $admite !== false]]
                : null,
            'aggregateRating' => $resenas->isNotEmpty()
                ? ['@type' => 'AggregateRating', 'ratingValue' => round($resenas->avg('estrellas'), 1), 'reviewCount' => $resenas->count()]
                : null,
        ], fn ($v) => $v !== null && $v !== []);
    }

    public function render()
    {
        $s = $this->sitio->loadMissing('municipio.provincia');
        $lugar = $s->municipio->nombre;
        $resenas = $s->resenas()->aprobadas()->with('user:id,name')->latest()->get();
        $miResena = auth()->check() ? $s->resenas()->where('user_id', auth()->id())->first() : null;
        $enViaje = auth()->check() ? auth()->user()->viaje()->whereKey($s->id)->first()?->pivot->estado : null;

        $descripcion = "{$s->nombre} ({$lugar}): {$s->etiquetaAdmite()}"
            .($s->tamano_maximo ? ". Tamaño máximo: {$s->tamano_maximo}" : '')
            .($s->otras_condiciones ? ". {$s->otras_condiciones}" : '');

        return view('livewire.ficha', [
            'resenas' => $resenas,
            'media' => $resenas->avg('estrellas'),
            'miResena' => $miResena,
            'enViaje' => $enViaje,
            'fotos' => $s->fotos()->publicadas()->get(),
            'mapaCfg' => mapa_config(),
            'cercanos' => $s->latitud
                ? Sitio::publicados()->whereKeyNot($s->id)->whereNotNull('latitud')
                    ->selectRaw('*, (POW(latitud - ?, 2) + POW((longitud - ?) * 0.8, 2)) AS d', [$s->latitud, $s->longitud])
                    ->with('municipio:id,nombre')->orderBy('d')->limit(4)->get()
                : collect(),
        ])->title("{$s->nombre}, {$lugar}")->layoutData([
            'descripcion' => mb_strimwidth($descripcion, 0, 158, '…'),
            'jsonLd' => $this->datosEstructurados($resenas),
        ]);
    }
}
