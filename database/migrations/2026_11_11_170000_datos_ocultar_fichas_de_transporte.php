<?php

use App\Models\Sitio;
use App\Support\DatosGuia;
use Illuminate\Database\Migrations\Migration;

// Natalia, 11/11/2026: el transporte ya está en la página «Transporte con mascota». Las fichas sueltas
// de tipo Transporte repetían esa información y pedían una ubicación que no tienen. Se ocultan (no se borran).
return new class extends Migration
{
    public function up(): void
    {
        // Base de datos vacía (tests o instalación nueva): no hay nada que cambiar
        if (! Sitio::exists()) {
            return;
        }

        DatosGuia::ocultar('Renfe Cercanías Cádiz', 'Varios (Bahía de Cádiz)');
        DatosGuia::ocultar('Autobús metropolitano (Consorcio de Transportes Bahía de Cádiz)', 'Varios (Bahía de Cádiz)');
        DatosGuia::ocultar('Autobuses urbanos de Cádiz', 'Cádiz');
        DatosGuia::ocultar('Catamarán de la Bahía de Cádiz', 'Varios (Bahía de Cádiz)');
        DatosGuia::ocultar('Ferry Baleària (Algeciras - Ceuta y Algeciras - Tánger Med)', 'Algeciras');
        DatosGuia::ocultar('Ferry FRS (Tarifa y Algeciras - Marruecos)', 'Tarifa');
        DatosGuia::ocultar('Ferry DFDS (rutas a Marruecos)', 'Tarifa');
    }

    public function down(): void
    {
        // Los cambios de datos no se deshacen
    }
};
