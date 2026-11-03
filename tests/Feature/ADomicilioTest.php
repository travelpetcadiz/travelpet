<?php

namespace Tests\Feature;

use App\Livewire\Explorar;
use App\Models\Municipio;
use App\Models\Provincia;
use App\Models\Sitio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ADomicilioTest extends TestCase
{
    use RefreshDatabase;

    private Municipio $chiclana;

    protected function setUp(): void
    {
        parent::setUp();
        $cadiz = Provincia::create(['nombre' => 'Cádiz']);
        $this->chiclana = Municipio::create(['provincia_id' => $cadiz->id, 'nombre' => 'Chiclana de la Frontera']);
    }

    private function sitio(array $datos): Sitio
    {
        return Sitio::create(['municipio_id' => $this->chiclana->id, 'tipo' => 'Servicios', ...$datos]);
    }

    public function test_al_marcarlo_a_domicilio_pierde_la_ubicacion_y_queda_en_el_historial(): void
    {
        $peluqueria = $this->sitio(['nombre' => 'Peluquería Guau', 'latitud' => 36.41, 'longitud' => -6.15]);

        $peluqueria->update(['a_domicilio' => true]);

        $this->assertNull($peluqueria->fresh()->latitud);
        $this->assertNull($peluqueria->fresh()->longitud);
        $this->assertDatabaseHas('cambios', ['sitio_id' => $peluqueria->id, 'campo' => 'latitud', 'antes' => '36.41']);
    }

    public function test_a_domicilio_no_cuenta_como_pendiente_de_ubicacion(): void
    {
        $this->sitio(['nombre' => 'Paseos Lola', 'a_domicilio' => true]);
        $this->sitio(['nombre' => 'Tienda sin ubicar']);
        $this->sitio(['nombre' => 'Solo latitud', 'latitud' => 36.41]);

        $this->assertSame(['Solo latitud', 'Tienda sin ubicar'], Sitio::sinUbicacion()->orderBy('nombre')->pluck('nombre')->all());
    }

    public function test_no_sale_en_el_mapa_y_su_ficha_lo_dice(): void
    {
        $domicilio = $this->sitio(['nombre' => 'Veterinaria a domicilio Kira', 'tipo' => 'Salud', 'a_domicilio' => true]);
        $this->sitio(['nombre' => 'Tienda Bigotes', 'latitud' => 36.42, 'longitud' => -6.14]);

        Livewire::withQueryParams(['vista' => 'mapa'])->test(Explorar::class)
            ->assertViewHas('puntos', fn ($puntos) => $puntos->pluck('nombre')->all() === ['Tienda Bigotes']);

        $this->get(route('sitio', $domicilio))->assertOk()->assertSee('Servicio a domicilio')->assertDontSee('Cómo llegar');
    }

    public function test_el_panel_muestra_la_casilla_y_el_filtro(): void
    {
        $admin = User::factory()->admin()->create();
        $this->sitio(['nombre' => 'Paseos Lola', 'a_domicilio' => true]);

        $this->actingAs($admin)->get('/admin/sitios/create')->assertOk()->assertSee('Servicio a domicilio (no tiene local)');
        $this->actingAs($admin)->get('/admin/sitios?filters[a_domicilio][value]=1')->assertOk()->assertSee('Paseos Lola');
    }
}
