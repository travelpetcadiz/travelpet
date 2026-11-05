<?php

use App\Models\Transporte;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Ryanair: comprobado por Natalia en su centro de ayuda el 5/11/2026
     * («No se permiten animales a bordo de ningún vuelo de Ryanair, excepto perros guía/de asistencia en determinadas rutas»).
     */
    public function up(): void
    {
        Transporte::where('medio', 'avion')->where('nombre', 'Ryanair')->get()->each(fn (Transporte $t) => $t->update([
            'estado' => 'no',
            'respuesta' => null,
            'en_cabina' => false,
            'otras_condiciones' => 'Solo perros guía y de asistencia, y solo en algunas rutas.',
            'enlace' => 'https://help.ryanair.com/hc/en-gb/articles/12890968181521-Does-Ryanair-carry-animals',
            'fecha_comprobacion' => '2026-11-05',
        ]));
    }

    public function down(): void {}
};
