<?php

use App\Models\Transporte;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Autobuses del Consorcio de la Bahía de Cádiz: mismas condiciones que el catamarán, según la misma
     * publicación oficial del Consorcio. Natalia lo pidió el 11/11/2026. Solo cambia la ficha si sigue
     * sin comprobar, para respetar lo que se haya cambiado en el panel.
     */
    public function up(): void
    {
        Transporte::where('medio', 'autobus')
            ->where('nombre', 'Autobuses del Consorcio de Transportes de la Bahía de Cádiz')
            ->where('estado', 'no_consta')
            ->update([
                'estado' => 'condiciones',
                'respuesta' => 'En transportín',
                'peso' => 'Hasta 10 kg',
                'transportin' => 'Cerrado, de 60 × 35 × 35 cm como máximo. No ocupa asiento',
                'otras_condiciones' => 'Una mascota por viajero.',
                'enlace' => 'https://cmtbc.es/',
                'fecha_comprobacion' => '2026-10-23',
            ]);
    }

    public function down(): void {}
};
