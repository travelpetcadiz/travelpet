<?php

use App\Models\Transporte;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Eurowings: requisitos de su web para mascotas en cabina, comprobados por Natalia el 5/11/2026.
     * El precio no venía en el texto, así que queda como «lo verás al reservar».
     */
    public function up(): void
    {
        Transporte::where('medio', 'avion')->where('nombre', 'Eurowings')->get()->each(fn (Transporte $t) => $t->update([
            'estado' => 'si',
            'respuesta' => 'En cabina',
            'en_cabina' => true,
            'peso' => '8 kg con la bolsa',
            'transportin' => '55 × 40 × 23 cm, bolsa blanda (no rígida)',
            'precio' => 'No lo hemos comprobado: lo verás al reservar',
            'otras_condiciones' => 'Desde 12 semanas de edad (15 si el vuelo es con Alemania). Hasta 2 animales por bolsa. Hay que rellenar un formulario y llevarlo al mostrador de facturación. No puedes ir en primera fila ni en las salidas de emergencia.',
            'enlace' => 'https://www.eurowings.com/es/informar/equipaje/viajar-con-animales.html',
            'fecha_comprobacion' => '2026-11-05',
        ]));
    }

    public function down(): void {}
};
