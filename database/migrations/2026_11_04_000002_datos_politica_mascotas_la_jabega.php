<?php

use App\Models\Sitio;
use App\Support\DatosGuia;
use Illuminate\Database\Migrations\Migration;

// Política de mascotas de La Jábega by Conil Home (documento del propio alojamiento, enviado por Natalia el 4/11/2026).
return new class extends Migration
{
    public function up(): void
    {
        // Base de datos vacía (tests o instalación nueva): no hay nada que corregir
        if (! Sitio::exists()) {
            return;
        }

        DatosGuia::actualizar('La Jábega by Conil Home', 'Conil de la Frontera', [
            'admite_perros' => 'condiciones',
            'tamano_maximo' => 'Máximo 1 o 2 mascotas, según el tamaño del alojamiento. No indican límite de tamaño',
            'suplemento' => '10 € por mascota y noche, más 20 € de limpieza el día de salida. Piden un depósito de 100 € con tarjeta que se devuelve si no hay daños',
            'sin_suplemento' => false,
            'zonas_permitidas' => 'En la habitación o el apartamento y en las zonas exteriores, siempre controladas. No pueden entrar en el desayuno',
            'otras_condiciones' => 'Admiten perros y gatos; con otras mascotas, preguntar antes. Tiene que estar vacunada y hay que avisar antes de llegar de qué mascota se trata. Con correa en las zonas comunes y sin dejarla sola mucho tiempo en la habitación. No se pueden usar las toallas ni las sábanas para asearla. Dan comedero y bebedero. Los perros de asistencia siempre pueden entrar',
            'fuente' => 'Política de mascotas de La Jábega by Conil Home (documento del alojamiento, enviado por Natalia el 04/11/2026)',
            'fuente_oficial' => true,
            'fecha_comprobacion' => '2026-11-04',
        ]);
    }

    public function down(): void
    {
        // Los datos no se deshacen
    }
};
