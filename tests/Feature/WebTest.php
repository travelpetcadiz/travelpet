<?php

namespace Tests\Feature;

use App\Livewire\Auth\Entrar;
use App\Livewire\Auth\Registro;
use App\Livewire\Explorar;
use App\Livewire\Ficha;
use App\Models\Foto;
use App\Models\Municipio;
use App\Models\Provincia;
use App\Models\Resena;
use App\Models\Sitio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class WebTest extends TestCase
{
    use RefreshDatabase;

    private Sitio $sitio;

    private Provincia $huelva;

    protected function setUp(): void
    {
        parent::setUp();
        $cadiz = Provincia::create(['nombre' => 'Cádiz']);
        $this->huelva = Provincia::create(['nombre' => 'Huelva']);
        $conil = Municipio::create(['provincia_id' => $cadiz->id, 'nombre' => 'Conil de la Frontera', 'comarca' => 'La Janda']);
        $ayamonte = Municipio::create(['provincia_id' => $this->huelva->id, 'nombre' => 'Ayamonte']);

        $this->sitio = Sitio::create(['municipio_id' => $conil->id, 'nombre' => 'Playa de los Bateles', 'tipo' => 'Playa', 'admite_perros' => 'si', 'latitud' => 36.27, 'longitud' => -6.09]);
        Sitio::create(['municipio_id' => $ayamonte->id, 'nombre' => 'Bar Ayamonte', 'tipo' => 'Restauración', 'admite_perros' => 'condiciones']);
        Sitio::create(['municipio_id' => $conil->id, 'nombre' => 'Sitio Oculto', 'tipo' => 'Playa', 'publicado' => false]);
    }

    public function test_las_paginas_publicas_cargan(): void
    {
        foreach (['/', '/como-funciona', '/entrar', '/registro', '/aviso-legal', '/privacidad', '/cookies', '/condiciones', '/sitemap.xml', '/sitio/'.$this->sitio->slug, '/mi-viaje', '/negocios', '/anade-travelpet-a-tu-movil'] as $url) {
            $this->get($url)->assertOk();
        }
        $this->get('/')->assertSee('¿Puedo ir con mi mascota?')->assertDontSee('mi perro');
    }

    public function test_explorar_filtra_por_provincia_y_oculta_lo_no_publicado(): void
    {
        Livewire::test(Explorar::class)
            ->assertSee('Playa de los Bateles')->assertSee('Bar Ayamonte')->assertDontSee('Sitio Oculto')
            ->set('provincia', 'huelva')
            ->assertSee('Bar Ayamonte')->assertDontSee('Playa de los Bateles');
    }

    public function test_la_portada_busca_al_enviar(): void
    {
        Livewire::test(Explorar::class)
            ->assertSee('¿A qué pueblo o sitio quieres ir?')
            ->assertSee('Cerca de mí')
            ->set('q', 'Bateles')
            ->call('buscar')
            ->assertSee('Playa de los Bateles')
            ->assertDontSee('Bar Ayamonte')
            ->assertDontSee('¿A qué pueblo o sitio quieres ir?');
    }

    public function test_el_carrusel_de_la_portada_muestra_las_fotos_de_portada_sin_credito_si_son_propias(): void
    {
        Foto::create(['archivo' => 'fotos/a.webp', 'descripcion' => 'Perro en la playa', 'autor' => 'Natalia', 'para_portada' => true]);
        Foto::create(['archivo' => 'fotos/b.webp', 'descripcion' => 'Perro en la marisma', 'autor' => 'Natalia', 'para_portada' => true]);
        Foto::create(['archivo' => 'fotos/c.webp', 'descripcion' => 'Foto que no es de portada', 'autor' => 'Natalia']);

        Livewire::test(Explorar::class)
            ->assertSee('Perro en la playa')->assertSee('Perro en la marisma')
            ->assertSee('Ver foto 2')
            ->assertDontSee('Foto que no es de portada')
            ->assertDontSee('Foto: Natalia');
    }

    public function test_la_foto_con_posicion_1_sale_la_primera_en_el_carrusel(): void
    {
        Foto::create(['archivo' => 'fotos/a.webp', 'descripcion' => 'Perrita en la marisma', 'para_portada' => true]);
        Foto::create(['archivo' => 'fotos/b.webp', 'descripcion' => 'Dos border collies en la playa', 'para_portada' => true, 'orden' => 1]);
        Foto::create(['archivo' => 'fotos/c.webp', 'descripcion' => 'Perro sentado', 'para_portada' => true]);

        $this->assertSame('Dos border collies en la playa', Foto::paraPortada()->first()->descripcion);
        Livewire::test(Explorar::class)->assertSeeInOrder(['Dos border collies en la playa', 'Perro sentado', 'Perrita en la marisma']);
    }

    public function test_la_paginacion_usa_flechas_y_numeros_en_espanol(): void
    {
        $conil = Municipio::firstWhere('nombre', 'Conil de la Frontera');
        foreach (range(1, 30) as $n) {
            Sitio::create(['municipio_id' => $conil->id, 'nombre' => "Bar {$n}", 'tipo' => 'Restauración', 'admite_perros' => 'si']);
        }

        Livewire::test(Explorar::class)
            ->set('tipo', 'Restauración')
            ->assertSee('Página siguiente')->assertSee('Página 1 de 2')
            ->assertDontSee('pagination.next')
            ->call('nextPage')
            ->assertSee('Página anterior')->assertSee('Página 2 de 2');
    }

    public function test_una_provincia_desactivada_no_sale_en_la_web(): void
    {
        $this->huelva->update(['activa' => false]);

        Livewire::test(Explorar::class)->assertDontSee('Bar Ayamonte');
        $this->get('/sitio/'.Sitio::firstWhere('nombre', 'Bar Ayamonte')->slug)->assertOk(); // la ficha sigue accesible por enlace directo
    }

    public function test_un_sitio_oculto_da_404(): void
    {
        $this->get('/sitio/'.Sitio::firstWhere('nombre', 'Sitio Oculto')->slug)->assertNotFound();
    }

    public function test_registro_crea_un_usuario_normal(): void
    {
        Livewire::test(Registro::class)
            ->set('name', 'Lucía')->set('email', 'lucia@example.com')
            ->set('password', 'secreto123')->set('password_confirmation', 'secreto123')
            ->set('acepto', true)
            ->call('registrar')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', ['email' => 'lucia@example.com', 'role' => 'usuario']);
        $this->assertAuthenticated();
    }

    public function test_registro_exige_aceptar_privacidad(): void
    {
        Livewire::test(Registro::class)
            ->set('name', 'Lucía')->set('email', 'lucia@example.com')
            ->set('password', 'secreto123')->set('password_confirmation', 'secreto123')
            ->call('registrar')
            ->assertHasErrors('acepto');
    }

    public function test_una_cuenta_desactivada_no_puede_entrar(): void
    {
        User::factory()->create(['email' => 'baneado@example.com', 'activo' => false]);

        Livewire::test(Entrar::class)
            ->set('email', 'baneado@example.com')->set('password', 'password')
            ->call('entrar')
            ->assertHasErrors('email');
        $this->assertGuest();
    }

    public function test_la_resena_se_publica_solo_cuando_se_aprueba(): void
    {
        $this->actingAs(User::factory()->create(['name' => 'Pepe']));

        Livewire::test(Ficha::class, ['sitio' => $this->sitio])
            ->set('estrellas', 4)->set('pudo_entrar', 'si')
            ->set('texto', 'Fuimos con nuestra perra grande y nos dejaron estar en la zona de la izquierda.')
            ->call('enviarResena')
            ->assertHasNoErrors();

        $resena = Resena::firstOrFail();
        $this->assertSame('pendiente', $resena->estado);
        $this->get('/sitio/'.$this->sitio->slug)->assertDontSee('nuestra perra grande');

        $resena->update(['estado' => 'aprobada']);
        $this->get('/sitio/'.$this->sitio->slug)->assertSee('nuestra perra grande');
    }

    public function test_mi_viaje_guarda_y_quita_sitios(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $ficha = Livewire::test(Ficha::class, ['sitio' => $this->sitio])->call('alternarViaje', 'quiero');
        $this->assertSame('quiero', $user->viaje()->first()->pivot->estado);

        $ficha->call('alternarViaje', 'quiero');
        $this->assertCount(0, $user->viaje()->get());
    }

    public function test_un_invitado_que_intenta_resenar_va_a_entrar(): void
    {
        Livewire::test(Ficha::class, ['sitio' => $this->sitio])
            ->set('texto', 'Texto suficientemente largo para validar.')
            ->call('enviarResena')
            ->assertRedirect('/entrar');
        $this->assertSame(0, Resena::count());
    }
}
