<?php

use App\Models\Sitio;
use App\Support\DatosGuia;
use Illuminate\Database\Migrations\Migration;

// Código de descuento de Cadizz Gestión Vacacional para quien venga de TravelPet (dato de Natalia, 8/11/2026).
// Se añade al final de «Información adicional» de todos sus alojamientos publicados, sin tocar el resto del texto.
return new class extends Migration
{
    private const CODIGO = 'TRAVELPETCADIZZ';

    private const TEXTO = '25 % de descuento con el código '.self::CODIGO.'.';

    public function up(): void
    {
        // Base de datos vacía (tests o instalación nueva): no hay nada que cambiar
        if (! Sitio::exists()) {
            return;
        }

        $alojamientos = Sitio::query()
            ->where('contacto', 'like', '%cadizz%')
            ->where('publicado', true)
            ->with('municipio')
            ->get();

        foreach ($alojamientos as $sitio) {
            if (str_contains((string) $sitio->otras_condiciones, self::CODIGO)) {
                continue;
            }

            DatosGuia::actualizar($sitio->nombre, $sitio->municipio->nombre, [
                'otras_condiciones' => trim($sitio->otras_condiciones.' '.self::TEXTO),
            ]);
        }
    }

    public function down(): void
    {
        // No se borra nada: el texto se cambia desde el panel
    }
};
