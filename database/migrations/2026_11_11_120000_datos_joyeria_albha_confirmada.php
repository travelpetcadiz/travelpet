<?php

use App\Models\Sitio;
use App\Support\DatosGuia;
use Illuminate\Database\Migrations\Migration;

// Joyería Albha (Jerez): la propia tienda confirmó por WhatsApp a Natalia el 11/11/2026
// que se puede entrar con perro, siempre que esté educado y vaya con correa.
return new class extends Migration
{
    public function up(): void
    {
        // Base de datos vacía (tests o instalación nueva): no hay nada que corregir
        if (! Sitio::exists()) {
            return;
        }

        DatosGuia::actualizar('Joyería Albha', 'Jerez de la Frontera', [
            'admite_perros' => 'condiciones',
            'zonas_permitidas' => 'Dentro de la tienda',
            'otras_condiciones' => 'Perros educados y con correa',
            'contacto' => '610 03 63 12 (WhatsApp)',
            'fuente' => 'La propia tienda, por WhatsApp, 11/11/2026',
            'fecha_comprobacion' => '2026-11-11',
            'verificado' => true,
            'fuente_oficial' => true,
        ]);
    }

    public function down(): void
    {
        // Los cambios de datos no se deshacen
    }
};
