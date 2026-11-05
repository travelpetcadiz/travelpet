<?php

namespace Tests\Feature;

use App\Models\Transporte;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransporteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Transporte::query()->delete();
    }

    public function test_la_pagina_muestra_las_companias_publicadas_y_lo_que_no_sabemos(): void
    {
        Transporte::create(['medio' => 'avion', 'nombre' => 'Aerolínea Prueba', 'estado' => 'si', 'respuesta' => 'En cabina', 'peso' => '8 kg con transportín', 'fecha_comprobacion' => '2026-11-05', 'enlace' => 'https://ejemplo.com/mascotas']);
        Transporte::create(['medio' => 'barco', 'nombre' => 'Naviera Prueba']);
        Transporte::create(['medio' => 'tren', 'nombre' => 'Tren Oculto', 'publicado' => false]);

        $this->get('/transporte-con-mascota')->assertOk()
            ->assertSee('Cómo llegar a Cádiz con tu mascota')
            ->assertSeeInOrder(['Aerolínea Prueba', 'En cabina', '8 kg con transportín', 'Comprobado el 5 de noviembre de 2026', 'Ver su norma'])
            ->assertSeeInOrder(['Naviera Prueba', 'Aún no lo hemos comprobado', 'Pendiente de comprobar'])
            ->assertDontSee('Tren Oculto')
            ->assertDontSee('href="#tren"', false);
    }

    public function test_se_enlaza_desde_el_menu_y_el_sitemap(): void
    {
        $this->get('/como-funciona')->assertSee(route('transporte'), false);
        $this->get('/sitemap.xml')->assertSee(route('transporte'), false);
    }

    public function test_el_equipo_la_gestiona_en_el_panel(): void
    {
        $transporte = Transporte::create(['medio' => 'avion', 'nombre' => 'Aerolínea Prueba']);
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)->get('/admin/transporte')->assertOk()->assertSee('Aerolínea Prueba');
        $this->actingAs($editor)->get("/admin/transporte/{$transporte->id}/edit")->assertOk();
        $this->actingAs($editor)->get('/admin/transporte/create')->assertOk();
    }
}
