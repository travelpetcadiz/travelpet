<?php

use App\Models\Sitio;
use App\Support\DatosGuia;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Marca como «a domicilio» los veterinarios y servicios para mascotas que lo dicen en su nombre
     * o en «qué es exactamente», y les quita la ubicación. El historial guarda las coordenadas que tenían.
     */
    public function up(): void
    {
        if (! Sitio::exists()) {
            return;
        }

        $antes = Sitio::$origenCambios;
        Sitio::$origenCambios = DatosGuia::ORIGEN;

        try {
            Sitio::query()
                ->whereIn('tipo', Sitio::SERVICIOS)
                ->where(fn ($q) => $q->where('nombre', 'like', '%domicilio%')->orWhere('subtipo', 'like', '%domicilio%'))
                ->each(fn (Sitio $sitio) => $sitio->update(['a_domicilio' => true]));
        } finally {
            Sitio::$origenCambios = $antes;
        }
    }

    public function down(): void {}
};
