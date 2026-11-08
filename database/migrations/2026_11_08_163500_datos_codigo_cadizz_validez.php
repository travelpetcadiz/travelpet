<?php

use App\Models\Sitio;
use App\Support\DatosGuia;
use Illuminate\Database\Migrations\Migration;

// Cadizz: el código TRAVELPETCADIZZ vale hasta el 31/12/2027 (captura de su motor de reservas, 8/11/2026).
// Añade la fecha al texto del código en «Información adicional» de todos sus alojamientos publicados.
return new class extends Migration
{
    private const VIEJO = 'Con el código TRAVELPETCADIZZ, la primera mascota no paga suplemento.';

    private const NUEVO = 'Con el código TRAVELPETCADIZZ, la primera mascota no paga suplemento (hasta el 31/12/2027).';

    public function up(): void
    {
        // Base de datos vacía (tests o instalación nueva): no hay nada que cambiar
        if (! Sitio::exists()) {
            return;
        }

        $alojamientos = Sitio::query()
            ->where('publicado', true)
            ->where('otras_condiciones', 'like', '%'.self::VIEJO.'%')
            ->with('municipio')
            ->get();

        foreach ($alojamientos as $sitio) {
            DatosGuia::actualizar($sitio->nombre, $sitio->municipio->nombre, [
                'otras_condiciones' => str_replace(self::VIEJO, self::NUEVO, (string) $sitio->otras_condiciones),
            ]);
        }
    }

    public function down(): void
    {
        // No se borra nada: el texto se cambia desde el panel
    }
};
