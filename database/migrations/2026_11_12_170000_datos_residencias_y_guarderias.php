<?php

use App\Models\Sitio;
use App\Support\DatosGuia;
use Illuminate\Database\Migrations\Migration;

// Residencias y guarderías de la hoja «TravelPet – Residencias y guarderías para revisar» (12/11/2026).
// Natalia pidió publicarlas a modo informativo con su teléfono, sin contactar con ellas: van sin «verificado»
// y sin punto en el mapa, porque ninguna fuente fiable da las coordenadas. Las descartadas (La Luna, Dogys
// Center, Can Arcos) no se añaden, y «La Escuela» es el mismo sitio que el Kennel que ya estaba en la guía.
return new class extends Migration
{
    public function up(): void
    {
        // Base de datos vacía (tests o instalación nueva): no hay nada que añadir
        if (! Sitio::exists()) {
            return;
        }

        $comun = ['tipo' => 'Servicios', 'admite_perros' => 'si', 'verificado' => false, 'fecha_comprobacion' => '2026-11-12'];

        DatosGuia::crearOActualizar('Rotaguau', 'Rota', [
            ...$comun,
            'subtipo' => 'Residencia / guardería / peluquería',
            'direccion' => 'Avda. María Auxiliadora, 65, local, 11520 Rota',
            'contacto' => '956 81 40 86 / 608 19 32 75',
            'otras_condiciones' => 'Residencia canina y felina con servicio de guardería y peluquería',
            'fuente' => 'https://www.inforota.es/2173-rotaguau.html ; https://rota.costasur.com/es/animales.html',
            'fuente_oficial' => false,
        ]);

        DatosGuia::crearOActualizar('Residencia Canina Canicen', 'Chiclana de la Frontera', [
            ...$comun,
            'subtipo' => 'Residencia canina',
            'direccion' => 'Camino del Molino Nuevo, 2, 11130 Chiclana de la Frontera',
            'contacto' => '639 55 45 35 / 635 80 29 39',
            'fuente' => 'http://www.canicen.com/residencia-canina/',
            'fuente_oficial' => true,
        ]);

        DatosGuia::crearOActualizar('Adiestramiento Canino El Almendral', 'Puerto Real', [
            ...$comun,
            'subtipo' => 'Residencia canina / adiestramiento',
            'direccion' => 'Camino del Almendral, 10, 11510 Puerto Real',
            'contacto' => '687 54 94 71',
            'fuente' => 'https://www.facebook.com/AdiestramientoCaninoElAlmendral/',
            'fuente_oficial' => true,
        ]);

        DatosGuia::crearOActualizar('Residencia Canina Cecasur', 'Jerez de la Frontera', [
            ...$comun,
            'subtipo' => 'Residencia canina',
            'direccion' => 'Ctra. N-IV (Madrid-Cádiz), km 627, 11401 Jerez de la Frontera',
            'contacto' => '676 71 84 79',
            'fuente' => 'https://www.facebook.com/p/Residencia-Canina-Cecasur-100076673725153/',
            'fuente_oficial' => true,
        ]);

        DatosGuia::crearOActualizar('Indiana Dog Hotel', 'Chiclana de la Frontera', [
            ...$comun,
            'subtipo' => 'Residencia canina',
            'direccion' => 'Camino Paloma de la Paz, 33, 11138 Chiclana de la Frontera',
            'contacto' => '618 27 76 36',
            'otras_condiciones' => 'Solo perros sociables con las vacunas y tratamientos al día. No admiten razas peligrosas ni perros agresivos. Visitas con cita',
            'fuente' => 'https://indianadoghotel.com/',
            'fuente_oficial' => true,
        ]);

        DatosGuia::crearOActualizar('Novocan', 'Chiclana de la Frontera', [
            ...$comun,
            'subtipo' => 'Residencia / guardería / peluquería',
            'direccion' => 'C.C. Aldea del Coto, local 18, 11130 Chiclana de la Frontera',
            'contacto' => '635 08 36 61',
            'otras_condiciones' => 'Hotel para mascotas y peluquería, con recogida y entrega a domicilio',
            'fuente' => 'https://novocan.es/',
            'fuente_oficial' => true,
        ]);

        DatosGuia::crearOActualizar('Residencia Canina Costa de la Luz', 'Chiclana de la Frontera', [
            ...$comun,
            'subtipo' => 'Residencia canina',
            'direccion' => 'Carretera del Marquesado, 11130 Chiclana de la Frontera',
            'contacto' => '620 06 80 45',
            'fuente' => 'http://www.actiweb.es/rccostadelaluz/',
            'fuente_oficial' => true,
        ]);

        DatosGuia::crearOActualizar('Guardería Canina Xerez', 'Jerez de la Frontera', [
            ...$comun,
            'subtipo' => 'Guardería canina',
            'direccion' => 'Barriada La Alegría, bloque 1, bajo dcha., 11401 Jerez de la Frontera',
            'contacto' => '680 41 25 49',
            'otras_condiciones' => 'Guardería y estancias de horas, días o semanas',
            'fuente' => 'https://okveterinarios.com/cadiz/jerez-de-la-frontera/guarderia-canina-xerez',
            'fuente_oficial' => false,
        ]);

        DatosGuia::crearOActualizar('Oh My Dog!', 'Cádiz', [
            ...$comun,
            'subtipo' => 'Peluquería y guardería canina',
            'direccion' => 'C/ Trille, 10, 11009 Cádiz',
            'contacto' => '640 26 58 89',
            'otras_condiciones' => 'Peluquería, tienda y guardería para perros y gatos',
            'fuente' => 'https://www.facebook.com/people/Oh-My-Dog/100089790079432/',
            'fuente_oficial' => true,
        ]);

        DatosGuia::crearOActualizar('Centro Canino Bahía de Cádiz', 'Puerto Real', [
            ...$comun,
            'subtipo' => 'Guardería canina',
            'direccion' => 'Ctra. de Paterna, km 2,7, 11510 Puerto Real',
            'contacto' => '605 91 49 58',
            'fuente' => 'https://hostelcanino.com/cadiz/centro-canino-bahia-de-cadiz/',
            'fuente_oficial' => false,
        ]);

        DatosGuia::crearOActualizar('Hotel Canino Arcos', 'Arcos de la Frontera', [
            ...$comun,
            'subtipo' => 'Residencia canina',
            'direccion' => 'Ctra. Arcos-Lebrija, km 0, 11630 Arcos de la Frontera',
            'contacto' => '633 09 61 07',
            'fuente' => 'https://guacan.es/cadiz/hotel-canino-arcos-en-arcos-de-la-frontera/',
            'fuente_oficial' => false,
        ]);

        // Ya estaban en la guía sin teléfono
        DatosGuia::actualizar('Residencia de Mascotas Los Perales', 'El Puerto de Santa María', [
            'contacto' => '654 36 36 33',
        ]);

        DatosGuia::actualizar('Centro Canino Campo de Gibraltar (Kennel)', 'Los Barrios', [
            'contacto' => '691 64 38 43',
        ]);
    }

    public function down(): void
    {
        //
    }
};
