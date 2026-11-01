<?php

namespace Tests\Feature;

use App\Filament\Resources\Sitios\Pages\EditSitio;
use App\Filament\Resources\Sitios\RelationManagers\FotosRelationManager;
use App\Models\Foto;
use App\Models\Municipio;
use App\Models\Provincia;
use App\Models\Sitio;
use App\Models\User;
use App\Support\Wikimedia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class FotosTest extends TestCase
{
    use RefreshDatabase;

    private Sitio $playa;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $cadiz = Provincia::create(['nombre' => 'Cádiz']);
        $conil = Municipio::create(['provincia_id' => $cadiz->id, 'nombre' => 'Conil de la Frontera']);
        $this->playa = Sitio::create(['municipio_id' => $conil->id, 'nombre' => 'Playa de Fuente del Gallo', 'tipo' => 'Playa', 'admite_perros' => 'condiciones']);
    }

    // Respuestas de Wikimedia Commons de ejemplo (búsqueda, datos de la foto y la imagen)
    private function wikimediaFalso(): void
    {
        Http::fake([
            'commons.wikimedia.org/w/api.php?*list=search*' => Http::response(['query' => ['search' => [
                ['title' => 'File:Fuente del Gallo-Conil-P1120916.jpg'],
                ['title' => 'File:Mapa de Conil.png'],
                ['title' => 'File:Otra playa cualquiera.jpg'],
            ]]]),
            'commons.wikimedia.org/w/api.php?*prop=imageinfo*' => Http::response(['query' => ['pages' => ['1' => [
                'title' => 'File:Fuente del Gallo-Conil-P1120916.jpg',
                'imageinfo' => [[
                    'mime' => 'image/jpeg',
                    'url' => 'https://upload.wikimedia.org/original.jpg',
                    'thumburl' => 'https://upload.wikimedia.org/thumb/1600px.jpg',
                    'descriptionurl' => 'https://commons.wikimedia.org/wiki/File:Fuente_del_Gallo-Conil-P1120916.jpg',
                    'extmetadata' => [
                        'Artist' => ['value' => '<a href="//commons.wikimedia.org/wiki/User:El_Pantera">El Pantera</a>'],
                        'LicenseShortName' => ['value' => 'CC BY-SA 4.0'],
                        'LicenseUrl' => ['value' => 'https://creativecommons.org/licenses/by-sa/4.0'],
                    ],
                ]],
            ]]]]),
            'upload.wikimedia.org/*' => Http::response('jpeg-de-prueba', 200, ['Content-Type' => 'image/jpeg']),
        ]);
    }

    public function test_buscar_en_wikimedia_crea_candidatas_sin_publicar_y_con_credito(): void
    {
        $this->wikimediaFalso();

        $this->assertSame(1, Wikimedia::crearCandidatas($this->playa)); // el mapa y la otra playa se descartan

        $foto = $this->playa->fotos()->first();
        $this->assertFalse($foto->publicada);
        $this->assertSame('El Pantera', $foto->autor);
        $this->assertSame('CC BY-SA 4.0', $foto->licencia);
        Storage::disk('public')->assertExists($foto->archivo);

        // Volver a buscar no la duplica
        $this->assertSame(0, Wikimedia::crearCandidatas($this->playa));
    }

    public function test_con_el_enlace_de_wikimedia_se_rellena_todo_solo(): void
    {
        $this->wikimediaFalso();

        $foto = $this->playa->fotos()->create([
            'origen' => 'wikimedia',
            'fuente_url' => 'https://commons.wikimedia.org/wiki/File:Fuente_del_Gallo-Conil-P1120916.jpg',
        ]);

        $this->assertSame('El Pantera', $foto->autor);
        $this->assertSame('https://creativecommons.org/licenses/by-sa/4.0', $foto->licencia_url);
        $this->assertNotNull($foto->archivo);
    }

    public function test_la_ficha_muestra_solo_las_fotos_publicadas_con_su_credito(): void
    {
        Storage::disk('public')->put('fotos/a.webp', 'x');
        Storage::disk('public')->put('fotos/b.webp', 'x');
        $this->playa->fotos()->create(['archivo' => 'fotos/a.webp', 'descripcion' => 'La playa al atardecer', 'origen' => 'wikimedia', 'autor' => 'El Pantera',
            'licencia' => 'CC BY-SA 4.0', 'licencia_url' => 'https://creativecommons.org/licenses/by-sa/4.0', 'fuente_url' => 'https://commons.wikimedia.org/wiki/File:X.jpg',
            'sin_personas' => true, 'publicada' => true]);
        $this->playa->fotos()->create(['archivo' => 'fotos/b.webp', 'descripcion' => 'Foto sin revisar', 'autor' => 'Natalia']);

        $this->get('/sitio/'.$this->playa->slug)
            ->assertSee('alt="La playa al atardecer"', false)
            ->assertSee('Foto: El Pantera')
            ->assertSee('CC BY-SA 4.0 · Wikimedia Commons')
            ->assertSee('href="https://creativecommons.org/licenses/by-sa/4.0"', false)
            ->assertDontSee('Foto sin revisar')
            ->assertSee('"image":[', false);
    }

    public function test_credito_segun_el_origen(): void
    {
        $this->assertSame('Foto cedida por Hotel Almadraba', (new Foto(['origen' => 'cedida', 'autor' => 'Hotel Almadraba']))->credito()['texto']);
        $this->assertSame('Foto: Natalia', (new Foto(['origen' => 'propia', 'autor' => 'Natalia']))->credito()['texto']);
    }

    public function test_el_panel_de_fotos_y_las_fotos_de_un_sitio_cargan(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        $this->get('/admin/fotos')->assertOk();
        $this->get('/admin/fotos/create')->assertOk()->assertSee('¿De dónde sale?');
        $this->get('/admin/sitios/'.$this->playa->slug.'/edit')->assertOk();
        // El bloque de fotos del sitio se carga aparte
        Livewire::test(FotosRelationManager::class, ['ownerRecord' => $this->playa, 'pageClass' => EditSitio::class])
            ->assertOk()
            ->assertSee('Buscar fotos libres en Wikimedia')
            ->assertSee('Este sitio aún no tiene fotos');
        $this->get('/admin/ayuda')->assertOk()->assertSee('Correo para pedir fotos a un negocio');
    }
}
