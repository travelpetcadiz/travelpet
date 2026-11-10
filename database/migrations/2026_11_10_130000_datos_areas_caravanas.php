<?php

use App\Models\Sitio;
use App\Support\DatosGuia;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Primeras áreas para caravanas y autocaravanas: solo las que dicen en su propia web
     * que admiten mascotas (comprobado el 10/11/2026). No están confirmadas por el propio sitio.
     * Sin coordenadas: las áreas privadas solo dan la ubicación exacta al reservar.
     */
    public function up(): void
    {
        if (! Sitio::exists()) {
            return;
        }

        $comun = [
            'tipo' => 'Caravanas',
            'subtipo' => 'Área de autocaravanas',
            'fuente_oficial' => true,
            'verificado' => false,
            'fecha_comprobacion' => '2026-11-10',
        ];

        DatosGuia::crearOActualizar('Camper Park Sanlúcar', 'Sanlúcar de Barrameda', [
            ...$comun,
            'direccion' => 'Ctra. de la Jara',
            'contacto' => '667 65 69 15 · 619 47 14 53 · camperparksanlucar.es',
            'admite_perros' => 'si',
            'otras_condiciones' => 'En su web dicen que las mascotas son bienvenidas. Parada de autobús en la entrada.',
            'fuente' => 'https://www.camperparksanlucar.es/',
        ]);

        DatosGuia::crearOActualizar('Área Sanlúcar AC Parking', 'Sanlúcar de Barrameda', [
            ...$comun,
            'contacto' => '619 26 13 25 · areasautocaravanas.com',
            'admite_perros' => 'si',
            'otras_condiciones' => 'A unos 50 m de la playa. La ubicación exacta te la mandan al reservar.',
            'fuente' => 'https://areasautocaravanas.com/en/areas/area-sanlucar-ac-parking-sanlucar-de-barrameda',
        ]);

        DatosGuia::crearOActualizar('Espacios Autocaravanas Valdevaqueros', 'Tarifa', [
            ...$comun,
            'contacto' => '619 26 13 25 · areasautocaravanas.com',
            'admite_perros' => 'condiciones',
            'suplemento' => '2,50 € por perro y día',
            'sin_suplemento' => false,
            'zonas_permitidas' => 'Con correa y bajo control; hay que recoger los excrementos',
            'otras_condiciones' => 'La ubicación exacta te la mandan al reservar.',
            'fuente' => 'https://areasautocaravanas.com/en/areas/area-de-tarifa',
        ]);

        DatosGuia::crearOActualizar('El Palmar Beach Caravan Area', 'Vejer de la Frontera', [
            ...$comun,
            'contacto' => '619 26 13 25 · areasautocaravanas.com',
            'admite_perros' => 'condiciones',
            'zonas_permitidas' => 'Con correa dentro del área; hay que recoger los excrementos',
            'otras_condiciones' => 'A un minuto a pie de la playa de El Palmar. La ubicación exacta te la mandan al reservar.',
            'fuente' => 'https://areasautocaravanas.com/en/areas/caravan-area-el-palmar-beach-vejer-de-la-frontera-cadiz/',
        ]);

        DatosGuia::crearOActualizar('Espacios Autocaravanas La Mimosa', 'Conil de la Frontera', [
            ...$comun,
            'contacto' => '619 26 13 25 · areasautocaravanas.com',
            'admite_perros' => 'si',
            'otras_condiciones' => 'Solo 5 vehículos. La ubicación exacta te la mandan al reservar.',
            'fuente' => 'https://areasautocaravanas.com/en/areas/aparcamiento-caravanas-la-mimosa/',
        ]);

        DatosGuia::crearOActualizar('Área Autocaravanas El Puerto de Santa María', 'El Puerto de Santa María', [
            ...$comun,
            'contacto' => '619 26 13 25 · areasautocaravanas.com',
            'admite_perros' => 'si',
            'otras_condiciones' => 'Junto al río. Solo autocaravanas: no admite caravanas. La ubicación exacta te la mandan al reservar.',
            'fuente' => 'https://www.areasautocaravanas.com/en/areas/puerto-de-santa-maria/',
        ]);
    }

    public function down(): void
    {
        //
    }
};
