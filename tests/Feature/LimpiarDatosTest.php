<?php

namespace Tests\Feature;

use App\Models\Sugerencia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LimpiarDatosTest extends TestCase
{
    use RefreshDatabase;

    private const MENSAJE = "Tipo: Dormir\nContacto (no se publica): recepcion@hotel.es\nAdmite: Sí";

    private function negocio(string $estado, int $mesesRevisada): Sugerencia
    {
        $sugerencia = Sugerencia::create(['tipo' => 'negocio', 'nombre' => 'Hotel', 'mensaje' => self::MENSAJE, 'estado' => $estado]);
        $sugerencia->timestamps = false;
        $sugerencia->forceFill(['updated_at' => now()->subMonths($mesesRevisada)])->save();

        return $sugerencia;
    }

    public function test_borra_el_contacto_de_negocios_revisados_hace_mas_de_12_meses(): void
    {
        $antigua = $this->negocio('hecha', 13);

        $this->artisan('travelpet:limpiar-datos')->assertSuccessful();

        $mensaje = $antigua->refresh()->mensaje;
        $this->assertStringNotContainsString('recepcion@hotel.es', $mensaje);
        $this->assertStringContainsString('Contacto (no se publica): [borrado]', $mensaje);
        $this->assertStringContainsString('Admite: Sí', $mensaje); // el resto del mensaje se queda
    }

    public function test_no_toca_el_contacto_si_es_reciente_o_esta_por_revisar(): void
    {
        $reciente = $this->negocio('hecha', 11);
        $porRevisar = $this->negocio('pendiente', 13);
        $otroTipo = Sugerencia::create(['tipo' => 'error', 'mensaje' => self::MENSAJE, 'estado' => 'hecha']);
        $otroTipo->timestamps = false;
        $otroTipo->forceFill(['updated_at' => now()->subMonths(13)])->save();

        $this->artisan('travelpet:limpiar-datos')->assertSuccessful();

        foreach ([$reciente, $porRevisar, $otroTipo] as $sugerencia) {
            $this->assertStringContainsString('recepcion@hotel.es', $sugerencia->refresh()->mensaje);
        }
    }
}
