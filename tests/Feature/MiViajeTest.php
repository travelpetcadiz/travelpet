<?php

namespace Tests\Feature;

use App\Livewire\MiViaje;
use App\Livewire\Negocios;
use App\Models\Municipio;
use App\Models\Provincia;
use App\Models\Sitio;
use App\Models\Sugerencia;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MiViajeTest extends TestCase
{
    use RefreshDatabase;

    private Sitio $playa;

    private Sitio $oculto;

    protected function setUp(): void
    {
        parent::setUp();
        $conil = Municipio::create(['provincia_id' => Provincia::create(['nombre' => 'Cádiz'])->id, 'nombre' => 'Conil de la Frontera']);
        $this->playa = Sitio::create(['municipio_id' => $conil->id, 'nombre' => 'Playa de los Bateles', 'tipo' => 'Playa', 'admite_perros' => 'si']);
        $this->oculto = Sitio::create(['municipio_id' => $conil->id, 'nombre' => 'Sitio Oculto', 'tipo' => 'Playa', 'publicado' => false]);
    }

    public function test_sin_cuenta_muestra_lo_guardado_en_el_movil_y_explica_para_que_sirve(): void
    {
        Livewire::test(MiViaje::class)
            ->assertSee('Cargando lo que has guardado')
            ->call('cargarLocal', [])
            ->assertSee('Planifica tu escapada entera')
            ->call('cargarLocal', [$this->playa->id => 'quiero', $this->oculto->id => 'quiero', 'x' => 'quiero'])
            ->assertSee('Playa de los Bateles')
            ->assertSee('se quedan en este móvil')
            ->assertDontSee('Sitio Oculto');
    }

    public function test_con_cuenta_guarda_quita_y_recoge_lo_guardado_antes_de_entrar(): void
    {
        $this->postJson(route('viaje.guardar'), ['items' => [$this->playa->id => 'quiero']])->assertUnauthorized();

        $user = User::factory()->create();
        $this->actingAs($user)
            ->postJson(route('viaje.guardar'), ['items' => [$this->playa->id => 'visitado', $this->oculto->id => 'quiero']])
            ->assertOk()
            ->assertExactJson(['items' => [(string) $this->playa->id => 'visitado']]);

        $this->actingAs($user)->postJson(route('viaje.guardar'), ['items' => [$this->playa->id => 'pirata']])->assertUnprocessable();

        $this->actingAs($user)->postJson(route('viaje.guardar'), ['items' => [$this->playa->id => null]])->assertExactJson(['items' => []]);
        $this->assertCount(0, $user->viaje()->get());
    }

    public function test_un_negocio_manda_sus_condiciones_sin_cuenta(): void
    {
        Livewire::test(Negocios::class)
            ->set('nombre', 'Bar Juanito')
            ->set('municipio', 'Jerez de la Frontera')
            ->set('tipo', 'Restauración')
            ->set('admite', 'condiciones')
            ->set('condiciones', 'En las mesas más tranquilas')
            ->set('contacto', 'bar@example.com')
            ->call('enviar')
            ->assertHasErrors('trabajaAqui')
            ->set('trabajaAqui', true)
            ->call('enviar')
            ->assertHasNoErrors()
            ->assertSee('Hemos recibido los datos de tu negocio');

        $sugerencia = Sugerencia::sole();
        $this->assertSame('negocio', $sugerencia->tipo);
        $this->assertSame('pendiente', $sugerencia->fresh()->estado);
        $this->assertStringContainsString('Sí, con condiciones', $sugerencia->mensaje);
        $this->assertStringContainsString('bar@example.com', $sugerencia->mensaje);
    }

    public function test_los_envios_de_robots_no_se_guardan(): void
    {
        Livewire::test(Negocios::class)
            ->set(['nombre' => 'Spam', 'municipio' => 'Cádiz', 'tipo' => 'Playa', 'admite' => 'si', 'contacto' => 'x', 'trabajaAqui' => true, 'web' => 'http://spam'])
            ->call('enviar')
            ->assertSet('enviado', true);

        $this->assertSame(0, Sugerencia::count());
    }
}
