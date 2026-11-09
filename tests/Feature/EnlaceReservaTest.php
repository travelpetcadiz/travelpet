<?php

namespace Tests\Feature;

use App\Filament\Resources\Sitios\SitioResource;
use App\Models\Municipio;
use App\Models\Provincia;
use App\Models\Sitio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnlaceReservaTest extends TestCase
{
    use RefreshDatabase;

    private Municipio $conil;

    protected function setUp(): void
    {
        parent::setUp();
        $cadiz = Provincia::create(['nombre' => 'Cádiz']);
        $this->conil = Municipio::create(['provincia_id' => $cadiz->id, 'nombre' => 'Conil de la Frontera']);
    }

    private function sitio(array $datos): Sitio
    {
        return Sitio::create(['municipio_id' => $this->conil->id, 'tipo' => 'Alojamiento', 'admite_perros' => 'si', ...$datos]);
    }

    public function test_un_alojamiento_con_enlace_muestra_el_boton_y_el_aviso_de_afiliado(): void
    {
        $hotel = $this->sitio(['nombre' => 'Hotel Prueba', 'enlace_reserva' => 'https://tp.media/r?marker=1&u=https%3A%2F%2Fwww.booking.com%2Fhotel%2Fes%2Fprueba.html']);

        $this->get(route('sitio', $hotel))->assertOk()
            ->assertSee('Ver precios y reservar')
            ->assertSee('Enlace de afiliado')
            ->assertSee('rel="sponsored noopener"', false);
    }

    public function test_sin_enlace_o_si_no_admite_mascotas_no_sale_el_boton(): void
    {
        $sinEnlace = $this->sitio(['nombre' => 'Hostal Sin Enlace']);
        $noAdmite = $this->sitio(['nombre' => 'Hotel No', 'admite_perros' => 'no', 'enlace_reserva' => 'https://tp.media/r?marker=1']);
        $bar = $this->sitio(['nombre' => 'Bar Con Enlace', 'tipo' => 'Restauración', 'enlace_reserva' => 'https://tp.media/r?marker=1']);

        foreach ([$sinEnlace, $noAdmite, $bar] as $sitio) {
            $this->get(route('sitio', $sitio))->assertOk()->assertDontSee('Ver precios y reservar');
        }
    }

    public function test_el_cambio_del_enlace_queda_en_el_historial_y_el_panel_tiene_el_campo(): void
    {
        $hotel = $this->sitio(['nombre' => 'Hotel Historial']);
        $hotel->update(['enlace_reserva' => 'https://tp.media/r?marker=1']);

        $this->assertDatabaseHas('cambios', ['sitio_id' => $hotel->id, 'campo' => 'enlace_reserva']);

        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)->get(SitioResource::getUrl('edit', ['record' => $hotel]))->assertOk()->assertSee('Enlace para reservar (afiliado)');
    }
}
