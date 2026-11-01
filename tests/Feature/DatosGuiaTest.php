<?php

namespace Tests\Feature;

use App\Models\Municipio;
use App\Models\Provincia;
use App\Models\Sitio;
use App\Support\DatosGuia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatosGuiaTest extends TestCase
{
    use RefreshDatabase;

    private Sitio $sitio;

    protected function setUp(): void
    {
        parent::setUp();
        $cadiz = Provincia::create(['nombre' => 'Cádiz']);
        $jerez = Municipio::create(['provincia_id' => $cadiz->id, 'nombre' => 'Jerez de la Frontera']);
        $this->sitio = Sitio::create(['municipio_id' => $jerez->id, 'nombre' => 'Bina Bar', 'tipo' => 'Restauración', 'admite_perros' => 'si', 'zonas_permitidas' => 'Escrito en el panel']);
    }

    public function test_actualizar_solo_toca_los_campos_indicados_y_deja_historial(): void
    {
        $this->assertTrue(DatosGuia::actualizar('Bina Bar', 'Jerez de la Frontera', ['suplemento' => 'Sin suplemento']));

        $this->sitio->refresh();
        $this->assertSame('Sin suplemento', $this->sitio->suplemento);
        $this->assertSame('Escrito en el panel', $this->sitio->zonas_permitidas);
        $this->assertDatabaseHas('cambios', ['sitio_id' => $this->sitio->id, 'campo' => 'suplemento', 'despues' => 'Sin suplemento', 'fuente' => 'GitHub']);
    }

    public function test_un_sitio_que_no_existe_no_rompe_nada(): void
    {
        $this->assertFalse(DatosGuia::actualizar('No existe', 'Jerez de la Frontera', ['suplemento' => 'x']));
        $this->assertFalse(DatosGuia::ocultar('No existe', 'Cádiz'));
    }

    public function test_crear_o_actualizar_crea_el_sitio_y_el_municipio_si_hace_falta(): void
    {
        $nuevo = DatosGuia::crearOActualizar('Parque canino Lola Flores', 'Arcos de la Frontera', ['tipo' => 'Espacio', 'admite_perros' => 'si'], comarca: 'Sierra de Cádiz');

        $this->assertTrue($nuevo->exists);
        $this->assertSame('Arcos de la Frontera', $nuevo->municipio->nombre);
        $this->assertSame('parque-canino-lola-flores-arcos-de-la-frontera', $nuevo->slug);

        // Ejecutarlo otra vez no duplica
        DatosGuia::crearOActualizar('Parque canino Lola Flores', 'Arcos de la Frontera', ['suplemento' => 'Gratis']);
        $this->assertSame(1, Sitio::where('nombre', 'Parque canino Lola Flores')->count());
    }

    public function test_ocultar_lo_quita_de_la_web_sin_borrarlo(): void
    {
        DatosGuia::ocultar('Bina Bar', 'Jerez de la Frontera');

        $this->assertFalse($this->sitio->refresh()->publicado);
        $this->get('/datos/sitios.json')->assertOk()->assertJsonPath('total', 0);
    }

    public function test_el_json_publico_trae_los_sitios_publicados(): void
    {
        $this->get('/datos/sitios.json')
            ->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('sitios.0.nombre', 'Bina Bar')
            ->assertJsonPath('sitios.0.municipio', 'Jerez de la Frontera');
    }
}
