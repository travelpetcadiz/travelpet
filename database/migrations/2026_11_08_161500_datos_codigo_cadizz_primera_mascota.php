<?php

use App\Models\Sitio;
use App\Support\DatosGuia;
use Illuminate\Database\Migrations\Migration;

// Cadizz confirma (8/11/2026): con el código TRAVELPETCADIZZ la primera mascota no paga los 25 € de suplemento.
// Sustituye el texto anterior («25 % de descuento…») en «Información adicional» de todos sus alojamientos publicados.
return new class extends Migration
{
    private const VIEJO = '25 % de descuento con el código TRAVELPETCADIZZ.';

    private const NUEVO = 'Con el código TRAVELPETCADIZZ, la primera mascota no paga suplemento.';

    public function up(): void
    {
        // Base de datos vacía (tests o instalación nueva): no hay nada que cambiar
        if (! Sitio::exists()) {
            return;
        }

        $alojamientos = Sitio::query()
            ->where('publicado', true)
            ->where(fn ($q) => $q
                ->where('contacto', 'like', '%cadizz%')
                ->orWhere('otras_condiciones', 'like', '%Cadizz Gestión Vacacional%'))
            ->with('municipio')
            ->get();

        foreach ($alojamientos as $sitio) {
            $texto = (string) $sitio->otras_condiciones;

            if (str_contains($texto, self::NUEVO)) {
                continue;
            }

            $texto = str_contains($texto, self::VIEJO)
                ? str_replace(self::VIEJO, self::NUEVO, $texto)
                : trim($texto.' '.self::NUEVO);

            DatosGuia::actualizar($sitio->nombre, $sitio->municipio->nombre, [
                'otras_condiciones' => $texto,
            ]);
        }
    }

    public function down(): void
    {
        // No se borra nada: el texto se cambia desde el panel
    }
};
