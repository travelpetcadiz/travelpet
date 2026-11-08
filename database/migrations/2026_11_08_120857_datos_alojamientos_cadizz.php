<?php

use App\Models\Sitio;
use App\Support\DatosGuia;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Log;

// Alquileres vacacionales de Cadizz Gestión Vacacional (cadizz.com), pedidos por Natalia el 8/11/2026.
// La gestora anuncia en su web que admite mascotas («Tu mascota también forma parte del viaje»).
// Solo se AÑADEN: si ya hay un sitio con un nombre parecido, no se toca nada de él.
// Dirección sin piso ni puerta, a petición de Natalia. Sin coordenadas por ahora.
return new class extends Migration
{
    private const FUENTE = 'Web de Cadizz Gestión Vacacional (cadizz.com), consultada el 08/11/2026';

    private const SIN_CONDICIONES = 'Aún no sabemos si cobran suplemento ni si hay límite de tamaño o número de mascotas.';

    public function up(): void
    {
        // Base de datos vacía (tests o instalación nueva): no hay nada que añadir
        if (! Sitio::exists()) {
            return;
        }

        $this->anadir('Andalusian Green Palace', 'Green Palace', 'El Puerto de Santa María', 'Calle Luna 51', 'Para 8 personas · 4 dormitorios · 2 baños · 130 m²');
        $this->anadir('Entre Cielo & Mar', 'Entre Cielo', 'El Puerto de Santa María', 'Calle Cielos 131', 'Para 9 personas · 4 dormitorios · 2 baños · 114 m²');
        $this->anadir('Ático El Falla', 'El Falla', 'Cádiz', 'Calle San Telmo 2', 'Para 2 personas · 1 dormitorio · 1 baño · 53 m² · Terraza de 40 m²');
        $this->anadir('El Faro del Manteca I', 'Faro del Manteca', 'Cádiz', 'Calle Corralón de los Carros 66', 'Para 4 personas · 1 dormitorio · 1 baño · 46 m² · Con terraza, junto a La Caleta');
        $this->anadir('El Cafetín 4', 'Cafetín 4', 'El Puerto de Santa María', 'Calle Santa María 4', 'Para 5 personas · 3 dormitorios · 1 baño · 78 m²');
        $this->anadir('Estudio Las Flores', 'Estudio Las Flores', 'El Puerto de Santa María', 'Calle Luna 9', 'Para 2 personas · 1 dormitorio · 1 baño · 28 m²');
        $this->anadir('Rincón de Azahar', 'Rincón de Azahar', 'El Puerto de Santa María', 'Calle de la Naranja (cerca de la playa de Fuentebravía)', 'Para 4 personas · 2 dormitorios · 1 baño · 65 m²');
    }

    private function anadir(string $nombre, string $parecido, string $municipio, string $direccion, string $detalles): void
    {
        if (Sitio::where('nombre', 'like', '%'.$parecido.'%')->exists()) {
            Log::info("Alojamientos Cadizz 8/11: «{$nombre}» ya está en la guía; no se toca.");

            return;
        }

        DatosGuia::crearOActualizar($nombre, $municipio, [
            'tipo' => 'Alojamiento',
            'subtipo' => 'Alquiler vacacional',
            'direccion' => $direccion,
            'contacto' => 'cadizz.com (Cadizz Gestión Vacacional)',
            'admite_perros' => 'si',
            'otras_condiciones' => $detalles.'. Lo gestiona Cadizz Gestión Vacacional. '.self::SIN_CONDICIONES,
            'fuente' => self::FUENTE,
            'fecha_comprobacion' => '2026-11-08',
            'verificado' => false,
            'fuente_oficial' => true,
        ]);
    }

    public function down(): void
    {
        // No se borra nada: si sobra alguno, se oculta desde el panel
    }
};
