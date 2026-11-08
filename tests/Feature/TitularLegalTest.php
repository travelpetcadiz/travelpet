<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TitularLegalTest extends TestCase
{
    use RefreshDatabase;

    public function test_aviso_legal_y_privacidad_muestran_a_la_titular_sin_pendientes(): void
    {
        config([
            'travelpet.titular.nombre' => 'Natalia Domínguez González',
            'travelpet.titular.nif' => '',
            'travelpet.titular.domicilio' => '',
        ]);

        foreach (['/aviso-legal', '/privacidad'] as $url) {
            $this->get($url)->assertOk()
                ->assertSee('Natalia Domínguez González')
                ->assertSee('travelpetcadiz@outlook.es')
                ->assertDontSee('PENDIENTE')
                ->assertDontSee('Domicilio:');
        }

        $this->get('/aviso-legal')->assertDontSee('NIF:');
    }

    public function test_si_hay_nif_y_domicilio_se_muestran(): void
    {
        config([
            'travelpet.titular.nif' => '12345678Z',
            'travelpet.titular.domicilio' => 'Calle Ejemplo 1, Chiclana',
        ]);

        $this->get('/aviso-legal')->assertSee('12345678Z')->assertSee('Calle Ejemplo 1, Chiclana');
        $this->get('/privacidad')->assertSee('NIF 12345678Z')->assertSee('Calle Ejemplo 1, Chiclana');
    }
}
