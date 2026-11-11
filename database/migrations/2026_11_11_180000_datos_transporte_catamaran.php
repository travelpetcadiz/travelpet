<?php

use App\Models\Transporte;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Catamarán de la Bahía de Cádiz en «Transporte con mascota», con los datos de su antigua ficha suelta
     * (ocultada el 11/11/2026). Natalia pidió añadirlo el 11/11/2026. Va el primero de los barcos.
     */
    public function up(): void
    {
        $orden = (Transporte::where('medio', 'barco')->min('orden') ?? 100) - 1;

        Transporte::firstOrCreate(['medio' => 'barco', 'nombre' => 'Catamarán de la Bahía de Cádiz'], [
            'zona' => 'Une Cádiz con El Puerto de Santa María y Rota',
            'estado' => 'condiciones',
            'respuesta' => 'En transportín',
            'peso' => 'Hasta 10 kg',
            'transportin' => 'Cerrado, de 60 × 35 × 35 cm como máximo. No ocupa asiento',
            'otras_condiciones' => 'Una mascota por viajero.',
            'enlace' => 'https://cmtbc.es/?op=maritimo',
            'fecha_comprobacion' => '2026-10-23',
            'orden' => $orden,
        ]);
    }

    public function down(): void {}
};
