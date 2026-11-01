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

// Para que Google encuentre cada ficha
Route::get('/sitemap.xml', function () {
    $sitios = Sitio::publicados()->get(['slug', 'updated_at']);

    return response()->view('sitemap', ['sitios' => $sitios])->header('Content-Type', 'application/xml');
})->name('sitemap');
