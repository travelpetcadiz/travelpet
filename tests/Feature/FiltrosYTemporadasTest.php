<?php

namespace Tests\Feature;

use App\Livewire\Explorar;
use App\Models\Municipio;
use App\Models\Provincia;
use App\Models\Sitio;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

class FiltrosYTemporadasTest extends TestCase
{
    use RefreshDatabase;

    private Municipio $conil;

    protected function setUp(): void
    {
        parent::setUp();
        $cadiz = Provincia::create(['nombre' => 'Cádiz']);
        $this->conil = Municipio::create(['provincia_id' => $cadiz->id, 'nombre' => 'Conil de la Frontera']);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function sitio(array $datos): Sitio
    {
        return Sitio::create(['municipio_id' => $this->conil->id, 'tipo' => 'Playa', 'admite_perros' => 'condiciones', ...$datos]);
    }

    private function el(string $fecha): Carbon
    {
        return Carbon::parse($fecha, 'Europe/Madrid');
    }

    public function test_playa_prohibida_en_temporada_de_bano(): void
    {
        $bateles = $this->sitio(['nombre' => 'Bateles', 'periodo_tipo' => 'prohibido', 'periodo_desde' => '01/05', 'periodo_hasta' => '31/10']);

        $this->assertSame(['puede' => true, 'texto' => 'Prohibido del 1 de mayo al 31 de octubre.'], $bateles->hoy($this->el('2026-11-02')));
        $this->assertSame(['puede' => false, 'texto' => 'No se puede ir hasta el 1 de noviembre.'], $bateles->hoy($this->el('2026-07-15')));
    }

    public function test_periodo_que_cruza_el_ano(): void
    {
        $chiclana = $this->sitio(['nombre' => 'Chiclana', 'periodo_tipo' => 'solo', 'periodo_desde' => '15/10', 'periodo_hasta' => '15/03']);

        $this->assertTrue($chiclana->hoy($this->el('2027-01-10'))['puede']);
        $this->assertTrue($chiclana->hoy($this->el('2026-10-15'))['puede']);
        $this->assertSame(['puede' => false, 'texto' => 'Se puede ir a partir del 15 de octubre.'], $chiclana->hoy($this->el('2026-06-01')));
    }

    public function test_domingo_de_ramos_cambia_cada_ano(): void
    {
        $cadiz = $this->sitio(['nombre' => 'Cádiz', 'periodo_tipo' => 'prohibido', 'periodo_desde' => 'ramos', 'periodo_hasta' => '30/09']);

        // En 2027 el Domingo de Ramos es el 21 de marzo
        $this->assertTrue($cadiz->hoy($this->el('2027-03-20'))['puede']);
        $this->assertFalse($cadiz->hoy($this->el('2027-03-21'))['puede']);
        // Pasada la temporada de 2026, se anuncia la de 2027
        $this->assertSame('Prohibido del 21 de marzo al 30 de septiembre.', $cadiz->hoy($this->el('2026-11-02'))['texto']);
    }

    public function test_sin_periodo_o_si_no_admite_no_se_dice_nada(): void
    {
        $this->assertNull($this->sitio(['nombre' => 'Sin fechas'])->hoy());
        $this->assertNull($this->sitio(['nombre' => 'Prohibida', 'admite_perros' => 'no', 'periodo_tipo' => 'todo'])->hoy());
        $this->assertTrue($this->sitio(['nombre' => 'Siempre', 'periodo_tipo' => 'todo'])->hoy()['puede']);
    }

    public function test_filtro_playas_abiertas_hoy(): void
    {
        Carbon::setTestNow($this->el('2026-07-15 12:00'));
        $this->sitio(['nombre' => 'Playa cerrada en verano', 'periodo_tipo' => 'prohibido', 'periodo_desde' => '01/05', 'periodo_hasta' => '31/10']);
        $this->sitio(['nombre' => 'Playa canina de verano', 'periodo_tipo' => 'solo', 'periodo_desde' => '01/06', 'periodo_hasta' => '30/09']);

        Livewire::test(Explorar::class)
            ->set('hoy', true)
            ->assertSee('Playa canina de verano')
            ->assertDontSee('Playa cerrada en verano');
    }

    public function test_filtros_practicos_solo_muestran_lo_seguro(): void
    {
        $this->sitio(['nombre' => 'Hotel grande', 'tipo' => 'Alojamiento', 'admite_grandes' => true, 'sin_suplemento' => true]);
        $this->sitio(['nombre' => 'Hotel pequeño', 'tipo' => 'Alojamiento', 'admite_grandes' => false, 'sin_suplemento' => false]);
        $this->sitio(['nombre' => 'Hotel sin datos', 'tipo' => 'Alojamiento']);

        Livewire::test(Explorar::class)
            ->set('grandes', true)
            ->assertSee('Hotel grande')->assertDontSee('Hotel pequeño')->assertDontSee('Hotel sin datos')
            ->set('grandes', false)->set('gratis', true)
            ->assertSee('Hotel grande')->assertDontSee('Hotel pequeño');
    }

    public function test_urgencias_y_cerca_de_mi_ordenan_por_distancia(): void
    {
        $base = ['tipo' => 'Salud', 'admite_perros' => 'si', 'urgencias_24h' => true];
        $this->sitio([...$base, 'nombre' => 'Vet lejana', 'latitud' => 36.70, 'longitud' => -6.12]);
        $this->sitio([...$base, 'nombre' => 'Vet cercana', 'latitud' => 36.28, 'longitud' => -6.09]);
        $this->sitio([...$base, 'nombre' => 'Vet sin urgencias', 'urgencias_24h' => false, 'latitud' => 36.27, 'longitud' => -6.08]);

        $this->get('/urgencias')->assertRedirect('/?urgencias=1');

        Livewire::withQueryParams(['urgencias' => 1])->test(Explorar::class)
            ->assertSee('Urgencias veterinarias 24 h')
            ->assertDontSee('Vet sin urgencias')
            ->call('cerca', 36.27, -6.09)
            ->assertSeeInOrder(['Vet cercana', 'Vet lejana'])
            ->assertSee('a 1,1 km');
    }

    public function test_en_urgencias_se_llama_al_numero_de_urgencias(): void
    {
        $base = ['tipo' => 'Salud', 'admite_perros' => 'si', 'urgencias_24h' => true];
        $animalia = $this->sitio([...$base, 'nombre' => 'Animalia', 'contacto' => '856101157', 'otras_condiciones' => 'Centro autorizado. Urgencias 24 h: 722 200 044']);
        $normal = $this->sitio([...$base, 'nombre' => 'Normal', 'contacto' => '956 34 32 14']);

        $this->assertSame('+34722200044', $animalia->telefonoUrgencias());
        $this->assertSame('+34956343214', $normal->telefonoUrgencias());

        Livewire::withQueryParams(['urgencias' => 1])->test(Explorar::class)
            ->assertSee('href="tel:+34722200044"', false)
            ->assertSee('Llamar a urgencias');
    }

    public function test_la_ficha_de_un_veterinario_de_urgencias_es_clara(): void
    {
        $avila = $this->sitio([
            'tipo' => 'Salud', 'admite_perros' => 'si', 'urgencias_24h' => true, 'nombre' => 'Clínica Avila Conil',
            'subtipo' => 'Clínica veterinaria', 'direccion' => 'C/ Magallanes 17, 11140 Conil de la Frontera',
            'contacto' => '956402972', 'otras_condiciones' => 'Centro autorizado. Urgencias 24 h: 956 870 717',
        ]);

        $this->get('/sitio/'.$avila->slug)
            ->assertSee('Urgencias las 24 horas')
            ->assertSee('Llamar a urgencias · 956 870 717')
            ->assertSee('href="tel:+34956870717"', false)
            ->assertSee('Llamar a la clínica · 956 402 972')
            ->assertSee('Información adicional')
            ->assertDontSee('Otras condiciones')
            ->assertSee('C/ Magallanes 17, 11140 Conil de la Frontera')
            ->assertDontSee('Conil de la Frontera, Conil de la Frontera')
            ->assertDontSee('Web y contacto');
    }

    public function test_la_ficha_avisa_a_google_de_que_admite_mascotas(): void
    {
        $hotel = $this->sitio(['nombre' => 'Hotel Conil', 'tipo' => 'Alojamiento', 'admite_perros' => 'si', 'contacto' => '956 44 55 66; https://hotelconil.es']);
        $playa = $this->sitio(['nombre' => 'Playa Bateles', 'admite_perros' => 'si']);

        $this->get('/sitio/'.$hotel->slug)
            ->assertSee('"@type":"LodgingBusiness"', false)
            ->assertSee('"petsAllowed":true', false)
            ->assertSee('"telephone":"+34956445566"', false)
            ->assertSee('Compartir');

        $this->get('/sitio/'.$playa->slug)
            ->assertSee('"@type":"Beach"', false)
            ->assertSee('"name":"Admite mascotas","value":true', false);
    }

    public function test_la_clasificacion_automatica_solo_marca_lo_claro(): void
    {
        $a = fn ($datos) => $this->sitio(['tipo' => 'Alojamiento', ...$datos]);
        $hasta10 = $a(['nombre' => 'A', 'tamano_maximo' => 'Hasta 10 kg. 1 perro por habitación', 'suplemento' => '12 €/noche']);
        $sinLimite = $a(['nombre' => 'B', 'tamano_maximo' => 'Sin límite de peso ni tamaño', 'suplemento' => 'Gratis. Fianza de 200 €']);
        $dudoso = $a(['nombre' => 'C', 'tamano_maximo' => 'Su política no indica límite de peso', 'suplemento' => 'Puede haber suplemento']);
        $concha = $this->sitio(['nombre' => 'D', 'subtipo' => 'Playa canina', 'otras_condiciones' => 'Perros nerviosos con correa']);
        $urbana = $this->sitio(['nombre' => 'E', 'subtipo' => 'Playa', 'zonas_permitidas' => 'Correa obligatoria']);

        (require database_path('migrations/2026_11_02_000003_datos_filtros_y_temporadas.php'))->up();

        $this->assertSame([false, false], [$hasta10->refresh()->admite_grandes, $hasta10->sin_suplemento]);
        $this->assertSame([true, true], [$sinLimite->refresh()->admite_grandes, $sinLimite->sin_suplemento]);
        $this->assertSame([null, null], [$dudoso->refresh()->admite_grandes, $dudoso->sin_suplemento]);
        $this->assertTrue($concha->refresh()->sin_correa);
        $this->assertFalse($urbana->refresh()->sin_correa);
        $this->assertDatabaseHas('cambios', ['sitio_id' => $sinLimite->id, 'campo' => 'admite_grandes', 'fuente' => 'Clasificación automática (GitHub)']);
    }
}
