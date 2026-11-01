<?php

namespace Tests\Feature;

use App\Models\Municipio;
use App\Models\Provincia;
use App\Models\Sitio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PanelTest extends TestCase
{
    use RefreshDatabase;

    private Sitio $sitio;

    protected function setUp(): void
    {
        parent::setUp();
        $provincia = Provincia::create(['nombre' => 'Cádiz', 'comunidad' => 'Andalucía']);
        $municipio = Municipio::create(['provincia_id' => $provincia->id, 'nombre' => 'Conil de la Frontera', 'comarca' => 'La Janda']);
        $this->sitio = Sitio::create(['municipio_id' => $municipio->id, 'nombre' => 'Hotel Prueba', 'tipo' => 'Alojamiento', 'admite_perros' => 'si']);
    }

    public static function paginasPanel(): array
    {
        return [
            ['/admin'], ['/admin/sitios'], ['/admin/sitios/create'], ['/admin/resenas'], ['/admin/sugerencias'],
            ['/admin/cambios'], ['/admin/municipios'], ['/admin/users'], ['/admin/provincias'],
            ['/admin/fotos'], ['/admin/fotos/create'], ['/mockup'],
        ];
    }

    #[DataProvider('paginasPanel')]
    public function test_el_admin_ve_todo_el_panel(string $url): void
    {
        $this->actingAs(User::factory()->admin()->create())->get($url)->assertOk();
    }

    public function test_el_admin_puede_editar_un_sitio(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get('/admin/sitios/'.$this->sitio->slug.'/edit')
            ->assertOk()
            ->assertSee('Hotel Prueba');
    }

    public function test_el_editor_gestiona_sitios_pero_no_usuarios_ni_provincias(): void
    {
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)->get('/admin/sitios')->assertOk();
        $this->actingAs($editor)->get('/admin/resenas')->assertOk();
        $this->actingAs($editor)->get('/admin/users')->assertForbidden();
        $this->actingAs($editor)->get('/admin/provincias')->assertForbidden();
    }

    public function test_los_bocetos_solo_los_ve_el_equipo(): void
    {
        $this->get('/mockup')->assertRedirect('/entrar');
        $this->actingAs(User::factory()->create())->get('/mockup')->assertForbidden();
        $this->actingAs(User::factory()->editor()->create())->get('/mockup')->assertOk()->assertSee('noindex', false);
        $this->get('/mockup/no-existe')->assertNotFound();
        $this->get('/mockup/..%2Findex')->assertNotFound();
    }

    public function test_un_usuario_normal_no_entra_al_panel(): void
    {
        $this->actingAs(User::factory()->create())->get('/admin')->assertForbidden();
    }

    public function test_un_admin_desactivado_no_entra_al_panel(): void
    {
        $this->actingAs(User::factory()->admin()->create(['activo' => false]))->get('/admin')->assertForbidden();
    }

    public function test_editar_un_sitio_queda_en_el_historial(): void
    {
        $editor = User::factory()->editor()->create(['name' => 'Ana']);
        $this->actingAs($editor);

        $this->sitio->update(['admite_perros' => 'condiciones', 'verificado' => true]);

        $this->assertDatabaseHas('cambios', ['sitio_id' => $this->sitio->id, 'campo' => 'admite_perros', 'antes' => 'si', 'despues' => 'condiciones', 'user_id' => $editor->id]);
        $this->assertDatabaseHas('cambios', ['sitio_id' => $this->sitio->id, 'campo' => 'verificado', 'antes' => 'No', 'despues' => 'Sí']);
    }
}
