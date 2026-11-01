<?php

use App\Livewire\Auth\Entrar;
use App\Livewire\Auth\Registro;
use App\Livewire\Explorar;
use App\Livewire\Ficha;
use App\Livewire\MiViaje;
use App\Livewire\Sugerir;
use App\Models\Sitio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::livewire('/', Explorar::class)->name('inicio');
Route::livewire('/sitio/{sitio}', Ficha::class)->name('sitio');

Route::view('/como-funciona', 'paginas.como-funciona')->name('como-funciona');
Route::view('/aviso-legal', 'legal.aviso')->name('legal.aviso');
Route::view('/privacidad', 'legal.privacidad')->name('legal.privacidad');

Route::middleware('guest')->group(function () {
    Route::livewire('/entrar', Entrar::class)->name('entrar');
    Route::livewire('/registro', Registro::class)->name('registro');
});

Route::middleware('auth')->group(function () {
    Route::livewire('/mi-viaje', MiViaje::class)->name('viaje');
    Route::livewire('/sugerir', Sugerir::class)->name('sugerir');

    Route::post('/salir', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('inicio');
    })->name('salir');
});

// Datos actuales en JSON (lo mismo que ya se ve en la web). Lo consulta Claude antes de
// preparar cambios de datos, porque desde el repositorio no ve la base de datos.
Route::get('/datos/sitios.json', function () {
    $sitios = Sitio::publicados()->with('municipio.provincia')->orderBy('id')->get()->map(fn (Sitio $s) => [
        'nombre' => $s->nombre,
        'municipio' => $s->municipio->nombre,
        'provincia' => $s->municipio->provincia->nombre,
        'comarca' => $s->municipio->comarca,
        'tipo' => $s->tipo,
        'subtipo' => $s->subtipo,
        'url' => route('sitio', $s),
        ...$s->only([
            'direccion', 'latitud', 'longitud', 'contacto', 'admite_perros', 'tamano_maximo', 'suplemento',
            'zonas_permitidas', 'temporada', 'otras_condiciones', 'fuente', 'verificado', 'fuente_oficial',
        ]),
        'fecha_comprobacion' => $s->fecha_comprobacion?->toDateString(),
    ]);

    return response()->json(['generado' => now()->toIso8601String(), 'total' => $sitios->count(), 'sitios' => $sitios], 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
})->name('datos.sitios');

// Para que Google encuentre cada ficha
Route::get('/sitemap.xml', function () {
    $sitios = Sitio::publicados()->get(['slug', 'updated_at']);

    return response()->view('sitemap', ['sitios' => $sitios])->header('Content-Type', 'application/xml');
})->name('sitemap');
