<?php

use App\Models\Sitio;
use App\Support\DatosGuia;
use Illuminate\Database\Migrations\Migration;

// Siete áreas y parkings para caravanas de la lista que pasó Natalia el 11/11/2026 (fuentes públicas).
// Pidió añadirlas todas sin «verificado»: las tres que dicen que admiten mascotas con su respuesta, y las
// cuatro dudosas como «Aún no lo sabemos». Coordenadas solo donde hay una fuente con el punto exacto.
return new class extends Migration
{
    public function up(): void
    {
        // Base de datos vacía (tests o instalación nueva): no hay nada que añadir
        if (! Sitio::exists()) {
            return;
        }

        $comun = ['tipo' => 'Caravanas', 'verificado' => false, 'fecha_comprobacion' => '2026-11-11'];

        DatosGuia::crearOActualizar('Torre Nueva Camper Park', 'Vejer de la Frontera', [
            ...$comun,
            'subtipo' => 'Área de autocaravanas',
            'direccion' => 'Camino de Torre Nueva, s/n, El Palmar',
            'latitud' => 36.2353,
            'longitud' => -6.0675,
            'contacto' => 'areaautocaravanaselpalmar.com',
            'admite_perros' => 'condiciones',
            'suplemento' => 'Sin suplemento',
            'sin_suplemento' => true,
            'sin_correa' => false,
            'zonas_permitidas' => 'Con correa dentro del recinto',
            'temporada' => 'Abierto todo el año',
            'otras_condiciones' => 'Entrada a cualquier hora con un código QR. Tiene agua, vaciado de aguas y duchas',
            'fuente' => 'Web del área (areaautocaravanaselpalmar.com); ubicación de su ficha en park4night, creada por el propio área',
            'fuente_oficial' => true,
        ]);

        DatosGuia::crearOActualizar('Palparadise', 'Vejer de la Frontera', [
            ...$comun,
            'subtipo' => 'Parking de autocaravanas',
            'direccion' => 'El Palmar',
            'contacto' => 'palparadise.com',
            'admite_perros' => 'si',
            'otras_condiciones' => 'Las mascotas van bajo la responsabilidad de sus dueños. Se entra a cualquier hora, con reserva. Según su web, ahora mismo no tiene electricidad, ni agua ni vaciado',
            'fuente' => 'Web del propio parking (palparadise.com)',
            'fuente_oficial' => true,
        ]);

        DatosGuia::crearOActualizar('Parking de autocaravanas de Puerto Serrano', 'Puerto Serrano', [
            ...$comun,
            'subtipo' => 'Parking de autocaravanas',
            'direccion' => 'Antigua estación de tren, al final de la Vía Verde de la Sierra',
            'latitud' => 36.92444,
            'longitud' => -5.55556,
            'admite_perros' => 'si',
            'suplemento' => 'Gratis',
            'otras_condiciones' => 'Área gratuita donde se puede pasar la noche',
            'fuente' => 'Ficha del área en AreasCamper',
            'fuente_oficial' => false,
        ]);

        DatosGuia::crearOActualizar('Área de autocaravanas Caños de Meca', 'Barbate', [
            ...$comun,
            'subtipo' => 'Área de autocaravanas',
            'direccion' => 'Los Caños de Meca',
            'contacto' => '606 92 82 10',
            'admite_perros' => 'no_consta',
            'otras_condiciones' => 'Área para autocaravanas y campers con agua y vaciado. Hay opiniones que dicen que admite mascotas, pero aún no lo ha confirmado el área',
            'fuente' => 'Ficha del área en Google',
            'fuente_oficial' => false,
        ]);

        DatosGuia::crearOActualizar('Parking de caravanas El Mini', 'Puerto Real', [
            ...$comun,
            'subtipo' => 'Parking de autocaravanas',
            'direccion' => 'Río San Pedro',
            'admite_perros' => 'no_consta',
            'otras_condiciones' => 'Aparcamiento público e informal, no es un área de servicios oficial. Gratis, según las opiniones de quienes han ido. Lo usan a menudo viajeros con perro',
            'fuente' => 'Opiniones en Google',
            'fuente_oficial' => false,
        ]);

        DatosGuia::crearOActualizar('Área de autocaravanas Puerto de Chipiona', 'Chipiona', [
            ...$comun,
            'subtipo' => 'Área de autocaravanas',
            'direccion' => 'Puerto deportivo de Chipiona',
            'admite_perros' => 'no_consta',
            'otras_condiciones' => 'Área de pago dentro del puerto deportivo, con entrada automática. Hay webs que dicen que admite mascotas; pregunta si hay límite de tamaño',
            'fuente' => 'Cadizfornia',
            'fuente_oficial' => false,
        ]);

        DatosGuia::crearOActualizar('La Morada del Sur', 'Jerez de la Frontera', [
            ...$comun,
            'subtipo' => 'Parking de autocaravanas',
            'direccion' => 'Av. del Tío Pepe, 21',
            'admite_perros' => 'no_consta',
            'otras_condiciones' => 'Parking vigilado con electricidad, wifi, duchas y cambio de aguas. Una web de áreas para autocaravanas incluye las mascotas entre sus servicios, pero aún no lo ha confirmado el propio parking',
            'fuente' => 'Ficha en una web de áreas para autocaravanas',
            'fuente_oficial' => false,
        ]);
    }

    public function down(): void {}
};
