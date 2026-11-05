<?php

namespace Tests\Feature;

use App\Models\Municipio;
use App\Models\Provincia;
use App\Models\Sitio;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaginasGuiaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $cadiz = Provincia::create(['nombre' => 'Cádiz']);
        $conil = Municipio::create(['provincia_id' => $cadiz->id, 'nombre' => 'Conil de la Frontera', 'comarca' => 'La Janda']);
        $tarifa = Municipio::create(['provincia_id' => $cadiz->id, 'nombre' => 'Tarifa', 'comarca' => 'Campo de Gibraltar']);

        foreach (['Playa de los Bateles', 'Playa de la Fontanilla', 'Playa del Palmar'] as $nombre) {
            Sitio::create(['municipio_id' => $conil->id, 'nombre' => $nombre, 'tipo' => 'Playa', 'admite_perros' => 'si']);
        }
        Sitio::create(['municipio_id' => $conil->id, 'nombre' => 'Bar La Ola', 'tipo' => 'Restauración', 'admite_perros' => 'condiciones', 'verificado' => true]);
        Sitio::create(['municipio_id' => $conil->id, 'nombre' => 'Playa Oculta', 'tipo' => 'Playa', 'publicado' => false]);
        Sitio::create(['municipio_id' => $tarifa->id, 'nombre' => 'Playa de Bolonia', 'tipo' => 'Playa', 'admite_perros' => 'no']);

        $huelva = Provincia::create(['nombre' => 'Huelva', 'activa' => false]);
        $ayamonte = Municipio::create(['provincia_id' => $huelva->id, 'nombre' => 'Ayamonte']);
        Sitio::create(['municipio_id' => $ayamonte->id, 'nombre' => 'Playa de Isla Canela', 'tipo' => 'Playa']);
    }

    public function test_cada_pagina_muestra_solo_sus_sitios_publicados(): void
    {
        $this->get('/guia/cadiz')->assertOk()
            ->assertSee('Sitios que admiten mascotas en la provincia de Cádiz')
            ->assertSee('Conil de la Frontera')->assertSee('Campo de Gibraltar');

        $this->get('/guia/cadiz/playas')->assertOk()
            ->assertSee('Playas para mascotas en la provincia de Cádiz')
            ->assertSee('Playa de Bolonia')->assertSee('Playa del Palmar')
            ->assertDontSee('Playa Oculta')->assertDontSee('Bar La Ola')->assertDontSee('Isla Canela');

        $this->get('/guia/cadiz/conil-de-la-frontera')->assertOk()
            ->assertSee('Sitios que admiten mascotas en Conil de la Frontera')
            ->assertSee('Bar La Ola')->assertDontSee('Playa de Bolonia');

        $this->get('/guia/cadiz/conil-de-la-frontera/playas')->assertOk()
            ->assertSee('Playas para mascotas en Conil de la Frontera')
            ->assertSee('Playa de los Bateles')->assertDontSee('Bar La Ola')->assertDontSee('noindex', false);
    }

    public function test_paginas_vacias_o_de_provincias_inactivas_no_existen(): void
    {
        $this->get('/guia/cadiz/tarifa/veterinarios')->assertNotFound();
        $this->get('/guia/cadiz/no-existe')->assertNotFound();
        $this->get('/guia/cadiz/conil-de-la-frontera/no-existe')->assertNotFound();
        $this->get('/guia/huelva')->assertNotFound();
        $this->get('/guia/huelva/playas')->assertNotFound();
    }

    public function test_las_paginas_con_pocos_sitios_no_se_indexan_ni_van_al_sitemap(): void
    {
        $this->get('/guia/cadiz/tarifa/playas')->assertOk()->assertSee('noindex', false);

        $this->get('/sitemap.xml')->assertOk()
            ->assertSee(url('/guia/cadiz'))
            ->assertSee(url('/guia/cadiz/playas'))
            ->assertSee(url('/guia/cadiz/conil-de-la-frontera/playas'))
            ->assertDontSee(url('/guia/cadiz/tarifa'))
            ->assertDontSee(url('/guia/cadiz/conil-de-la-frontera/restaurantes-y-bares'))
            ->assertDontSee(url('/guia/huelva'));
    }
}
