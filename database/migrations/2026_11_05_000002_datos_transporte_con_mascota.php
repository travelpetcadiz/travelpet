<?php

use App\Models\Transporte;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Primeros datos de «Transporte con mascota», sacados de la web oficial de cada compañía el 5/11/2026.
     * Lo que no se pudo comprobar queda como «Aún no lo hemos comprobado». Luego se cambia en el panel.
     */
    public function up(): void
    {
        if (Transporte::exists()) {
            return;
        }

        $hoy = '2026-11-05';
        $filas = [
            // Avión: primero las que vuelan a Jerez (Aena, aeropuerto de Jerez)
            ['avion', 'Iberia', 'Vuela a Jerez, Sevilla y Málaga', true, true, 'si', 'En cabina y en bodega',
                '8 kg con transportín', '45 × 35 × 25 cm (blando en los vuelos de Air Nostrum)',
                '40 € por trayecto en España (44 € en el aeropuerto); 60 € en el resto de Europa',
                'En cabina: perros, gatos, tortugas y aves. En bodega: solo perros y gatos de hasta 45 kg con transportín, pidiéndolo al menos 48 horas antes (desde 90 € por trayecto, se paga en el aeropuerto). En vuelos con Reino Unido hay más límites.',
                'https://www.iberia.com/gb/fly-with-iberia/pets/', $hoy],
            ['avion', 'Vueling', 'Vuela a Jerez, Sevilla y Málaga', true, true, 'si', 'Solo en cabina',
                '10 kg con transportín', '45 × 39 × 21 cm, no rígido',
                'Su web no lo dice: lo verás al reservar',
                'Hasta 2 perros o gatos de la misma camada en un transportín. Nunca en bodega. No pueden ir en primera fila ni junto a las salidas de emergencia.',
                'https://www.vueling.com/es/servicios-vueling/prepara-tu-viaje/mascotas-a-bordo', $hoy],
            ['avion', 'Volotea', 'Vuela a Jerez, Sevilla y Málaga', true, true, 'si', 'En cabina',
                '10 kg con transportín', '50 × 40 × 20 cm, no rígido',
                '39 € por trayecto en su web (60 € en el aeropuerto)',
                'Solo perros y gatos pequeños, desde 8 semanas. 1 mascota por pasajero y 5 por vuelo: mira si quedan plazas.',
                'https://www.volotea.com/es/tu-reserva/mascotas-a-bordo/', $hoy],
            ['avion', 'Binter', 'Vuela a Jerez y Sevilla', true, true, 'si', 'En cabina y en bodega',
                'Hasta 8 kg con transportín en cabina; más, en bodega', '45 × 35 × 25 cm en cabina',
                '25 € por trayecto en cabina; 100 € en bodega',
                'Mira si tu ruta tiene este servicio antes de comprar el billete.',
                'https://www.bintercanarias.com/en/information/pets', $hoy],
            ['avion', 'TUI fly (Alemania)', 'Vuela a Jerez', true, true, 'condiciones', 'En cabina, solo en algunas rutas',
                '8 kg con transportín', null,
                'Su web no lo dice: pregúntalo al reservar',
                'Solo perros y gatos. Se reserva escribiendo a la compañía. No en vuelos con Reino Unido.',
                'https://www.tuifly.com/de/faqs', $hoy],
            ['avion', 'Eurowings', 'Vuela a Jerez, Sevilla y Málaga', true, null, 'no_consta', null, null, null, null,
                'Pregúntalo a la compañía antes de comprar el billete.',
                'https://www.eurowings.com/es/informar/equipaje/viajar-con-animales.html', null],
            ['avion', 'Air Europa', 'Vuela a Sevilla y Málaga', false, true, 'si', 'En cabina y en bodega',
                '8 kg la mascota y 10 kg con el transportín', '40 × 30 × 30 cm, blando',
                '35 € en España; 50 € en el resto de Europa (su web no aclara si es por trayecto)',
                'Solo perros y gatos, desde 3 meses. En bodega, hasta 50 kg con transportín rígido (90 € en España).',
                'https://www.aireuropa.com/de/en/aea/informacion-para-volar/pasajeros/mascotas.html', $hoy],
            ['avion', 'British Airways', 'Vuela a Sevilla y Málaga', false, false, 'condiciones', 'Solo en bodega',
                null, null, null,
                'No lleva mascotas en cabina en ninguna ruta, solo perros de asistencia.',
                'https://www.britishairways.com/content/information/travel-assistance/travelling-with-pets', $hoy],
            ['avion', 'easyJet', 'Vuela a Sevilla y Málaga', false, false, 'no', null,
                null, null, null,
                'Solo perros de asistencia.',
                'https://www.easyjet.com/es/ayuda/embarcar-y-volar/asistencia-especial', $hoy],
            ['avion', 'Ryanair', 'Vuela a Sevilla y Málaga', false, null, 'no_consta', null, null, null, null,
                'Pregúntalo a la compañía antes de comprar el billete.',
                'https://help.ryanair.com/hc/en-gb/articles/12890968181521-Does-Ryanair-carry-animals', null],

            // Tren
            ['tren', 'Renfe: AVE, Alvia, Intercity, Avlo y Media Distancia', 'Trenes a Cádiz, Jerez, El Puerto y Algeciras', false, null, 'si', 'Admite mascotas',
                'Hasta 10 kg en transportín. Perros de hasta 40 kg sin transportín en casi todos los trenes', 'Mascotas pequeñas: 60 × 35 × 35 cm',
                'Mascota pequeña: 10 € en AVE y Avlo (gratis con Prémium), 25 % del billete en Media Distancia. Perro grande: 40 € de suplemento',
                'Perros grandes: 2 por tren, mayores de 1 año, con correa corta y bozal en la estación y al subir y bajar. No en trenes con enlace ni con autobús alternativo.',
                'https://www.renfe.com/es/es/viajar/informacion-util/mascotas', $hoy],
            ['tren', 'Renfe: Cercanías', 'Cercanías de Cádiz', false, null, 'si', 'Admite mascotas, gratis',
                'Sin límite', 'No hace falta', 'Gratis, sin billete',
                'Con correa y bozal. 1 mascota por persona.',
                'https://www.renfe.com/es/es/viajar/informacion-util/mascotas', $hoy],

            // Autobús
            ['autobus', 'Autobuses del Consorcio de Transportes de la Bahía de Cádiz', 'Entre los pueblos de la provincia', false, null, 'no_consta', null, null, null, null,
                'Pregúntalo antes de subir.', null, null],
            ['autobus', 'Autobús urbano de Cádiz', 'Dentro de la ciudad de Cádiz', false, null, 'no_consta', null, null, null, null,
                'Hemos leído que desde octubre de 2026 se puede ir con mascotas pequeñas en transportín, pero aún no lo hemos visto en una fuente oficial.', null, null],

            // Barco
            ['barco', 'Baleària', 'De Algeciras a Ceuta y a Tánger Med', false, null, 'no_consta', null, null, null, null,
                'Para ir a Ceuta piden microchip, vacuna de la rabia puesta en los últimos 12 meses y pasaporte europeo. Las condiciones a bordo aún no las hemos comprobado.',
                'https://helpcenter.balearia.com/hc/es/articles/4414952381073-Pasajeros-que-viajan-a-Ceuta-o-Melilla-con-mascota', null],
            ['barco', 'DFDS (antes FRS)', 'De Tarifa a Tánger y de Algeciras a Ceuta y a Tánger Med', false, null, 'no_consta', null, null, null, null,
                'Pregúntalo a la compañía antes de comprar el billete.',
                'https://www.dfds.com/es-es/ferris-de-pasajeros/rutas-de-ferry/ferry-espana-marruecos/tarifa-tanger-ville', null],
        ];

        foreach ($filas as $i => [$medio, $nombre, $zona, $jerez, $cabina, $estado, $respuesta, $peso, $transportin, $precio, $otras, $enlace, $fecha]) {
            Transporte::create([
                'medio' => $medio, 'nombre' => $nombre, 'zona' => $zona, 'vuela_a_jerez' => $jerez, 'en_cabina' => $cabina,
                'estado' => $estado, 'respuesta' => $respuesta, 'peso' => $peso, 'transportin' => $transportin, 'precio' => $precio,
                'otras_condiciones' => $otras, 'enlace' => $enlace, 'fecha_comprobacion' => $fecha, 'orden' => ($i + 1) * 10,
            ]);
        }
    }

    public function down(): void {}
};
