<?php

use App\Models\Sitio;
use App\Support\DatosGuia;
use Illuminate\Database\Migrations\Migration;

// Cadizz: Southern Owl Penthouse y Corazón de Cádiz La Caleta no salen en la lista de alojamientos
// que admiten mascotas que mandó Cadizz (revisado por Natalia el 8/11/2026). Se ocultan, no se borran.
return new class extends Migration
{
    public function up(): void
    {
        // Base de datos vacía (tests o instalación nueva): no hay nada que cambiar
        if (! Sitio::exists()) {
            return;
        }

        DatosGuia::ocultar('Southern Owl Penthouse', 'Cádiz');
        DatosGuia::ocultar('Corazón de Cádiz La Caleta', 'Cádiz');
    }

    public function down(): void
    {
        // No se borra nada: se pueden volver a mostrar desde el panel
    }
};
