<?php

namespace Tests\Feature;

use App\Models\Municipio;
use App\Models\Provincia;
use App\Models\Sitio;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SepararCategoriasTest extends TestCase
{
    use RefreshDatabase;

    public function test_cada_sitio_de_transporte_y_ocio_va_a_su_categoria_nueva(): void
    {
        $cadiz = Provincia::create(['nombre' => 'Cádiz']);
        $tarifa = Municipio::create(['provincia_id' => $cadiz->id, 'nombre' => 'Tarifa']);
        $crear = fn (string $nombre, ?string $subtipo) => Sitio::create(['municipio_id' => $tarifa->id, 'nombre' => $nombre, 'tipo' => 'Transporte y ocio', 'subtipo' => $subtipo]);

        $ferry = $crear('Ferry a Tánger', 'Ferry');
        $bus = $crear('Autobuses Comes', 'Autobús');
        $libreria = $crear('Librería Mares', 'Librería');
        $tienda = $crear('Tienda del Puerto', 'Comercio');
        $castillo = $crear('Castillo de Guzmán', 'Monumento');
        $escuela = $crear('Club canino', 'Centro de entrenamiento');
        $sinSubtipo = $crear('Sin subtipo', null);

        (require database_path('migrations/2026_11_10_120000_separar_transporte_y_ocio.php'))->up();

        $this->assertSame('Transporte', $ferry->fresh()->tipo);
        $this->assertSame('Transporte', $bus->fresh()->tipo);
        $this->assertSame('Comercio', $libreria->fresh()->tipo);
        $this->assertSame('Comercio', $tienda->fresh()->tipo);
        $this->assertSame('Ocio', $castillo->fresh()->tipo);
        $this->assertSame('Ocio', $escuela->fresh()->tipo);
        $this->assertSame('Ocio', $sinSubtipo->fresh()->tipo);
        $this->assertSame(0, Sitio::where('tipo', 'Transporte y ocio')->count());
    }

    public function test_las_direcciones_antiguas_llevan_a_planes(): void
    {
        $cadiz = Provincia::create(['nombre' => 'Cádiz']);
        Municipio::create(['provincia_id' => $cadiz->id, 'nombre' => 'Tarifa']);

        $this->get('/guia/cadiz/transporte-y-ocio')->assertRedirect('/guia/cadiz/planes')->assertStatus(301);
        $this->get('/guia/cadiz/tarifa/transporte-y-ocio')->assertRedirect('/guia/cadiz/tarifa/planes')->assertStatus(301);
    }
}
