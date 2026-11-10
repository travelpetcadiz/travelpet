<?php

use App\Models\Sitio;
use App\Support\DatosGuia;
use Illuminate\Database\Migrations\Migration;

// Bungalows Costa de la Luz (Barbate): es un solo alojamiento con 2 bungalows, no una cadena
// (revisado a petición de Natalia el 10/11/2026). Se completa la ficha con su anuncio en Andalucía Sur.
// Sigue sin confirmar: falta preguntar al dueño por suplemento y límites.
return new class extends Migration
{
    public function up(): void
    {
        // Base de datos vacía (tests o instalación nueva): no hay nada que cambiar
        if (! Sitio::exists()) {
            return;
        }

        DatosGuia::actualizar('Bungalows Costa de La Luz', 'Barbate', [
            'subtipo' => 'Bungalows',
            'direccion' => 'Zona Los Veteranos, s/n. A 3 km de Barbate y a 200 m del parque natural de La Breña',
            'latitud' => 36.2070760,
            'longitud' => -5.9342490,
            'otras_condiciones' => 'Son 2 bungalows independientes, cada uno para hasta 4 personas y con piscina privada. Aún no sabemos si cobran suplemento ni si hay límite de tamaño o número',
            'fuente' => 'Anuncio del propietario en Andalucía Sur (andaluciasur.com/alojamientos/cadiz/barbate/bungalowscostadelaluz), consultado el 10/11/2026, y viajacontumascota.es',
            'fecha_comprobacion' => '2026-11-10',
        ]);
    }

    public function down(): void
    {
        // Los cambios de datos no se deshacen
    }
};
