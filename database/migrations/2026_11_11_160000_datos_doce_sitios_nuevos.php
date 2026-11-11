<?php

use App\Models\Sitio;
use App\Support\DatosGuia;
use Illuminate\Database\Migrations\Migration;

// Los 12 sitios de «Añadir» de la hoja «TravelPet – Sitios nuevos para revisar (11/11)».
// Natalia pidió cargarlos todos el 11/11/2026. Ninguno está confirmado por el propio sitio ni visto por ella,
// así que van sin «verificado». Coordenadas solo donde hay una fuente fiable; el resto, sin punto en el mapa.
return new class extends Migration
{
    public function up(): void
    {
        // Base de datos vacía (tests o instalación nueva): no hay nada que añadir
        if (! Sitio::exists()) {
            return;
        }

        $comun = ['verificado' => false, 'fecha_comprobacion' => '2026-11-11'];

        DatosGuia::crearOActualizar('Centro Comercial Bahía Sur', 'San Fernando', [
            ...$comun,
            'tipo' => 'Comercio',
            'subtipo' => 'Centro comercial',
            'direccion' => 'Av. Caño Herrera, s/n',
            'latitud' => 36.4714009,
            'longitud' => -6.2090782,
            'admite_perros' => 'condiciones',
            'zonas_permitidas' => 'En la galería comercial',
            'otras_condiciones' => 'Siempre con correa corta y llevando la cartilla de identificación y vacunación. Los perros de razas potencialmente peligrosas, además, con bozal. Hay una ducha canina en el paseo marítimo del centro, junto a la Puerta 5',
            'fuente' => 'Nota del propio centro publicada en Diario Bahía de Cádiz',
            'fuente_oficial' => true,
        ]);

        DatosGuia::crearOActualizar('LUZ Shopping', 'Jerez de la Frontera', [
            ...$comun,
            'tipo' => 'Comercio',
            'subtipo' => 'Centro comercial',
            'direccion' => 'Ronda Aurora Boreal, s/n',
            'latitud' => 36.6939603,
            'longitud' => -6.15977672,
            'contacto' => '956 35 74 40 · luzshopping.com',
            'admite_perros' => 'si',
            'otras_condiciones' => 'El centro, al aire libre, se anuncia como Pet Friendly y tiene una Doggy Zone y tiendas de artículos para mascotas. Cada tienda decide si se puede entrar con ella',
            'fuente' => 'Web de INGKA Centres, propietario del centro (ingkacentres.com)',
            'fuente_oficial' => true,
        ]);

        DatosGuia::crearOActualizar('Parque canino de la plaza de Santa Ana', 'Cádiz', [
            ...$comun,
            'tipo' => 'Espacio',
            'subtipo' => 'Parque canino',
            'direccion' => 'Plaza de Santa Ana',
            'admite_perros' => 'si',
            'zonas_permitidas' => 'Recinto vallado de 986 m²',
            'otras_condiciones' => 'Con fuente, zona de agility, bancos y dispensador de bolsas, según el Ayuntamiento al abrirlo en 2019',
            'fuente' => 'Nota del Ayuntamiento de Cádiz publicada en Diario Bahía de Cádiz (2019)',
            'fuente_oficial' => true,
        ]);

        DatosGuia::crearOActualizar('Parque canino de El Altillo', 'Jerez de la Frontera', [
            ...$comun,
            'tipo' => 'Espacio',
            'subtipo' => 'Parque canino',
            'direccion' => 'Parque de El Altillo. Entrada por la avenida del Altillo',
            'admite_perros' => 'si',
            'zonas_permitidas' => 'Recinto vallado para perros',
            'otras_condiciones' => 'Con fuente bebedero y zona para cachorros. Abierto por el Ayuntamiento en agosto de 2022',
            'fuente' => 'Ayuntamiento de Jerez, notas de prensa de julio y agosto de 2022 (jerez.es)',
            'fuente_oficial' => true,
        ]);

        DatosGuia::crearOActualizar('Sendero del río Majaceite', 'El Bosque', [
            ...$comun,
            'tipo' => 'Espacio',
            'subtipo' => 'Ruta / sendero',
            'direccion' => 'Sale de El Bosque (junto al albergue) y llega a Benamahoma',
            'latitud' => 36.7575,
            'longitud' => -5.5114,
            'admite_perros' => 'condiciones',
            'zonas_permitidas' => 'Todo el sendero',
            'otras_condiciones' => 'Unos 5 km de ida, dificultad baja, con sombra y junto al río. Está dentro del Parque Natural Sierra de Grazalema: lleva a tu mascota con correa',
            'fuente' => 'Decathlon Outdoor (ruta accesible con perro) y Cádiz Turismo (datos de la ruta)',
            'fuente_oficial' => false,
        ]);

        DatosGuia::crearOActualizar('Sendero El Tesorillo', 'Grazalema', [
            ...$comun,
            'tipo' => 'Espacio',
            'subtipo' => 'Ruta / sendero',
            'direccion' => 'Junto al área recreativa Los Llanos del Campo, cerca de Benamahoma',
            'admite_perros' => 'condiciones',
            'otras_condiciones' => 'Turismo Andaluz lo propone entre sus rutas aptas para ir con perro. Está en el Parque Natural Sierra de Grazalema: lleva a tu mascota con correa e infórmate antes, porque algunas zonas protegidas limitan la entrada de animales',
            'fuente' => 'Turismo Andaluz (andalucia.org), «Rutas senderistas para hacer con tu perro por Andalucía»',
            'fuente_oficial' => true,
        ]);

        DatosGuia::crearOActualizar('Vía Verde Entre Ríos (Rota–Chipiona–Sanlúcar)', 'Chipiona', [
            ...$comun,
            'tipo' => 'Espacio',
            'subtipo' => 'Vía verde',
            'direccion' => 'Por la antigua vía del tren, de Rota a Sanlúcar de Barrameda pasando por Chipiona',
            'admite_perros' => 'condiciones',
            'zonas_permitidas' => 'Todo el recorrido',
            'otras_condiciones' => 'Algo más de 16 km. Con correa. Lleva agua para tu mascota. El tramo de Rota a Costa Ballena tiene su propia ficha en la guía',
            'fuente' => 'Turismo Andaluz (andalucia.org), «5 actividades para hacer con tu perro si visitas Cádiz»',
            'fuente_oficial' => true,
        ]);

        DatosGuia::crearOActualizar('Ruta del Almarchal (río Cachón)', 'Barbate', [
            ...$comun,
            'tipo' => 'Espacio',
            'subtipo' => 'Ruta / sendero',
            'direccion' => 'A orillas del río Cachón, en Zahara de los Atunes',
            'admite_perros' => 'si',
            'otras_condiciones' => 'Turismo Andaluz la recomienda para hacer senderismo con tu mascota. No da más condiciones',
            'fuente' => 'Turismo Andaluz (andalucia.org), «5 actividades para hacer con tu perro si visitas Cádiz»',
            'fuente_oficial' => true,
        ]);

        DatosGuia::crearOActualizar('Ruta de las Calas de Roche', 'Conil de la Frontera', [
            ...$comun,
            'tipo' => 'Espacio',
            'subtipo' => 'Ruta / sendero',
            'direccion' => 'Sale del final de la playa de La Barrosa',
            'admite_perros' => 'condiciones',
            'zonas_permitidas' => 'Por el camino de arriba',
            'otras_condiciones' => 'Ruta circular de algo más de 6 km por un camino de arena y pasarelas de madera. Ojo: en las calas no se admiten perros en ningún momento del año (ordenanza de playas de Conil)',
            'fuente' => 'Turismo Andaluz (andalucia.org), «5 actividades para hacer con tu perro si visitas Cádiz»; ordenanza de playas de Conil',
            'fuente_oficial' => true,
        ]);

        DatosGuia::crearOActualizar('Faro de Camarinal y Torre de Gracia', 'Tarifa', [
            ...$comun,
            'tipo' => 'Espacio',
            'subtipo' => 'Ruta / sendero',
            'direccion' => 'Punta Camarinal, entre Atlanterra y Bolonia',
            'latitud' => 36.09014151,
            'longitud' => -5.810462,
            'admite_perros' => 'si',
            'zonas_permitidas' => 'Paseo al aire libre hasta el faro y la torre',
            'otras_condiciones' => 'Turismo Andaluz lo recomienda para ir con perro. No da condiciones',
            'fuente' => 'Turismo Andaluz (andalucia.org), «5 actividades para hacer con tu perro si visitas Cádiz»',
            'fuente_oficial' => true,
        ]);

        DatosGuia::crearOActualizar('Visita guiada al Castillo de San Sebastián', 'Cádiz', [
            ...$comun,
            'tipo' => 'Ocio',
            'subtipo' => 'Visita guiada',
            'direccion' => 'Sale de la Puerta de La Caleta',
            'latitud' => 36.5285,
            'longitud' => -6.3114,
            'contacto' => 'Se reserva en civitatis.com',
            'admite_perros' => 'si',
            'otras_condiciones' => 'Visita de pago de unas 2 horas con guía en español. La ficha de la actividad dice que se admiten mascotas',
            'fuente' => 'Ficha de la actividad en Civitatis',
            'fuente_oficial' => false,
        ]);

        DatosGuia::crearOActualizar('Free tour por Cádiz', 'Cádiz', [
            ...$comun,
            'tipo' => 'Ocio',
            'subtipo' => 'Visita guiada',
            'direccion' => 'Por el centro histórico de Cádiz',
            'admite_perros' => 'condiciones',
            'zonas_permitidas' => 'En las zonas al aire libre del recorrido',
            'otras_condiciones' => 'Visita a pie con guía. Tu mascota no puede entrar en los interiores',
            'fuente' => 'Comiviajeros, «Playas para perros en Cádiz» (actualizado en agosto de 2026)',
            'fuente_oficial' => false,
        ]);
    }

    public function down(): void
    {
        // Los cambios de datos no se deshacen
    }
};
