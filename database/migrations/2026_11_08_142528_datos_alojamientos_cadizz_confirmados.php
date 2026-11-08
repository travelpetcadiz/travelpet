<?php

use App\Models\Sitio;
use App\Support\DatosGuia;
use Illuminate\Database\Migrations\Migration;

// Respuesta de Cadizz Gestión Vacacional a TravelPet (8/11/2026), con su lista de alojamientos que admiten mascotas.
// - Confirma 4 de los que ya estaban y oculta los 3 que no admiten mascotas.
// - Añade Villa Verde (Chiclana; Natalia pone la dirección en el panel) y 11 alojamientos nuevos.
// Condiciones comunes: 25 € por mascota y estancia, sin límite de tamaño, máximo 2 mascotas
// (Villa Verde, hasta 4). Piden huéspedes responsables y no dejar solas a las mascotas que lo pasan mal.
return new class extends Migration
{
    private const FUENTE = 'Cadizz Gestión Vacacional, en respuesta a TravelPet el 08/11/2026';

    private const GESTORA = 'Lo gestiona Cadizz Gestión Vacacional.';

    private const NORMAS = 'Piden huéspedes responsables y no dejar sola a la mascota en el alojamiento si lo pasa mal.';

    public function up(): void
    {
        // Base de datos vacía (tests o instalación nueva): no hay nada que cambiar
        if (! Sitio::exists()) {
            return;
        }

        // Ya estaban en la guía: ahora confirmados por Cadizz
        $this->confirmar('Entre Cielo & Mar', 'El Puerto de Santa María', 'Para 8 personas · 4 dormitorios · 2 baños · 114 m²');
        $this->confirmar('Estudio Las Flores', 'El Puerto de Santa María', 'Para 2 personas · 1 dormitorio · 1 baño · 28 m²');
        $this->confirmar('Ático El Falla', 'Cádiz', 'Para 2 personas · 1 dormitorio · 1 baño · 53 m² · Terraza de 40 m²');
        $this->confirmar('El Faro del Manteca I', 'Cádiz', 'Para 4 personas · 1 dormitorio · 1 baño · 46 m² · Con terraza, junto a La Caleta');

        // No admiten mascotas
        DatosGuia::ocultar('Andalusian Green Palace', 'El Puerto de Santa María');
        DatosGuia::ocultar('El Cafetín 4', 'El Puerto de Santa María');
        DatosGuia::ocultar('Rincón de Azahar', 'El Puerto de Santa María');

        // Nuevos
        $this->anadir('Villa Verde', 'Chiclana de la Frontera', null, 'Para 8 personas', null, 4);

        $verano = 'Solo en julio y agosto';
        $this->anadir('Escolano', 'Puerto Real', 'Calle Escolano Moreno 3', null, $verano);
        $this->anadir('Luz de la Bahía', 'Puerto Real', 'Calle Concepción 5', null, $verano);
        $this->anadir('Mar y Sal', 'Puerto Real', 'Calle Concepción 7', null, $verano);
        $this->anadir('Brisa de Trafalgar', 'Puerto Real', 'Calle Trafalgar 7', null, $verano);
        $this->anadir('Bahía Urbana', 'Puerto Real', 'Calle Alameda 3', null, $verano);
        $this->anadir('Mirador Barragán', 'Puerto Real', 'Calle Barragán 3', null, $verano);
        $this->anadir('Amanecer de La Cachucha', 'Puerto Real', 'Calle Ángel 1', null, $verano);
        $this->anadir('Bergantín', 'Puerto Real', 'Calle Bergantín 1B', 'Con vistas al mar', $verano);
        $this->anadir('Palmeras del Mar', 'Puerto Real', 'Calle Palmeras 1', null, $verano);

        $this->anadir('Southern Owl Penthouse', 'Cádiz', 'Calle Buenos Aires 18', 'Ático de 100 m² con terraza privada', 'Solo en Semana Santa y en verano');
        $this->anadir('Corazón de Cádiz La Caleta', 'Cádiz', 'Calle Hospital de Mujeres 39', null, 'Solo en verano');
    }

    private function confirmar(string $nombre, string $municipio, string $detalles): void
    {
        DatosGuia::actualizar($nombre, $municipio, [
            ...$this->condiciones(),
            'otras_condiciones' => $detalles.'. Máximo 2 mascotas. '.self::NORMAS.' '.self::GESTORA,
        ]);
    }

    private function anadir(string $nombre, string $municipio, ?string $direccion, ?string $detalles, ?string $temporada, int $maximoMascotas = 2): void
    {
        DatosGuia::crearOActualizar($nombre, $municipio, [
            'tipo' => 'Alojamiento',
            'subtipo' => 'Alquiler vacacional',
            'direccion' => $direccion,
            'contacto' => 'cadizz.com (Cadizz Gestión Vacacional)',
            'temporada' => $temporada,
            ...$this->condiciones(),
            'otras_condiciones' => ($detalles ? $detalles.'. ' : '')."Máximo {$maximoMascotas} mascotas. ".self::NORMAS.' '.self::GESTORA,
        ]);
    }

    /** @return array<string, mixed> */
    private function condiciones(): array
    {
        return [
            'admite_perros' => 'condiciones',
            'suplemento' => '25 € por mascota y estancia',
            'sin_suplemento' => false,
            'tamano_maximo' => 'Sin límite de tamaño',
            'admite_grandes' => true,
            'fuente' => self::FUENTE,
            'fecha_comprobacion' => '2026-11-08',
            'verificado' => true,
            'fuente_oficial' => true,
        ];
    }

    public function down(): void
    {
        // No se borra nada: si sobra alguno, se oculta desde el panel
    }
};
