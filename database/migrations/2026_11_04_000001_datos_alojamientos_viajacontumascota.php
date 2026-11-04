<?php

use App\Models\Sitio;
use App\Support\DatosGuia;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Log;

// Alojamientos de la provincia que aparecen en viajacontumascota.es (pedido por Natalia el 4/11/2026).
// Solo se AÑADEN: si ya hay un sitio con un nombre parecido (por ejemplo, porque Natalia lo ha metido
// en el panel), no se toca nada de él.
// Fuera de esta tanda: Punta Candor Suites (ya estaba en la guía), Apartamento Oh Cádiz (la ficha no dice
// nada de mascotas) y Apartamentos La Residencia Tarifa (la ficha no se pudo leer).
return new class extends Migration
{
    private const FUENTE = 'viajacontumascota.es (guía de alojamientos con mascotas), consultada el 04/11/2026: ';

    public function up(): void
    {
        // Base de datos vacía (tests o instalación nueva): no hay nada que añadir
        if (! Sitio::exists()) {
            return;
        }

        $this->anadir('Quintas de Luna', 'Quintas de Luna', 'Vejer de la Frontera', [
            'subtipo' => 'Casa rural',
            'direccion' => 'Zahora 375 (Zahora). Bungalows en el camino a la playa de Manguetas; la casa, cerca de la Venta Curro',
            'latitud' => 36.2083980,
            'longitud' => -6.0353237,
            'contacto' => '630 404 859 / 648 264 642',
            'admite_perros' => 'si',
            'zonas_permitidas' => 'Parcela vallada con jardín privado en todos los alojamientos',
            'otras_condiciones' => 'Bungalows, estudio y casa independientes. En el estudio la valla es «antifugas» y la mascota puede quedarse sola en la parcela, con porche, sombra y agua. Aún no sabemos si cobran suplemento ni si hay límite de tamaño o número',
            'fuente' => self::FUENTE.'https://viajacontumascota.es/alojamiento-quintas-de-luna_2330.html',
        ]);

        $this->anadir('Bungalows Puravida', 'Puravida', 'Barbate', [
            'subtipo' => 'Casa rural',
            'direccion' => 'Ctra. de los Caños, 13, Zahora',
            'latitud' => 36.1998490,
            'longitud' => -6.0373788,
            'contacto' => '649 601 643 / 956 393 371',
            'admite_perros' => 'condiciones',
            'otras_condiciones' => 'Mascotas bien controladas y sin ir sueltas por las instalaciones. Aún no sabemos si cobran suplemento ni si hay límite de tamaño o número',
            'fuente' => self::FUENTE.'https://viajacontumascota.es/alojamiento-bungalows-puravida_1101.html',
        ]);

        $this->anadir('Bungalows El Cañita', 'Cañita', 'Barbate', [
            'subtipo' => 'Casa rural',
            'direccion' => 'Avda. Trafalgar, 53, Los Caños de Meca',
            'latitud' => 36.1850805,
            'longitud' => -6.0098762,
            'contacto' => '956 437 034 / 606 553 351',
            'admite_perros' => 'si',
            'suplemento' => 'Gratis',
            'sin_suplemento' => true,
            'zonas_permitidas' => 'Jardín privado delimitado',
            'otras_condiciones' => 'Aún no sabemos si hay límite de tamaño o número',
            'fuente' => self::FUENTE.'https://viajacontumascota.es/alojamiento-bungalows-el-ca-ita_2407.html',
        ]);

        $this->anadir('Bungalows Costa de La Luz', 'Bungalows Costa de la Luz', 'Barbate', [
            'subtipo' => 'Casa rural',
            'direccion' => 'Los Veteranos, s/n (zona rural)',
            'contacto' => '649 518 405 / 648 141 085',
            'admite_perros' => 'si',
            'otras_condiciones' => 'Aún no sabemos si cobran suplemento ni si hay límite de tamaño o número',
            'fuente' => self::FUENTE.'https://viajacontumascota.es/alojamiento-bungalows-costa-de-la-luz_2019.html',
        ]);

        $this->anadir('La Jábega by Conil Home', 'Jábega', 'Conil de la Frontera', [
            'subtipo' => 'Apartamentos turísticos',
            'direccion' => 'Calle Chiclana, 9',
            'latitud' => 36.2773360,
            'longitud' => -6.0920100,
            'contacto' => '621 21 21 67 · https://conilhome.com/es/la-jabega-conil/',
            'admite_perros' => 'si',
            'tamano_maximo' => 'Sin límite de raza ni tamaño',
            'admite_grandes' => true,
            'otras_condiciones' => 'Dan comedero y bebedero. Aún no sabemos si cobran suplemento ni cuántas mascotas admiten',
            'fuente' => self::FUENTE.'https://viajacontumascota.es/alojamiento-la-j-bega-by-conil-home_3068.html',
        ]);

        $this->anadir('Apartamentos Puerto Sherry', 'Puerto Sherry', 'El Puerto de Santa María', [
            'subtipo' => 'Apartamentos turísticos',
            'direccion' => 'Paseo de la Bahía, 76 (Puerto Sherry)',
            'latitud' => 36.5790798,
            'longitud' => -6.2620987,
            'contacto' => '674 008 727 / 692 643 396',
            'admite_perros' => 'si',
            'otras_condiciones' => 'Aún no sabemos si cobran suplemento ni si hay límite de tamaño o número',
            'fuente' => self::FUENTE.'https://viajacontumascota.es/alojamiento-apartamentos-puerto-sherry_2033.html',
        ]);

        $this->anadir('Boat Haus - Casas Flotantes', 'Boat Haus', 'La Línea de la Concepción', [
            'subtipo' => 'Casa flotante',
            'direccion' => 'Avda. Príncipe de Asturias (puerto deportivo)',
            'latitud' => 36.1567258,
            'longitud' => -5.3559487,
            'contacto' => '673 569 012 · boat-haus.com',
            'admite_perros' => 'condiciones',
            'otras_condiciones' => 'Hasta 2 mascotas por barco. Aún no sabemos si cobran suplemento ni si hay límite de tamaño',
            'fuente' => self::FUENTE.'https://viajacontumascota.es/alojamiento-boat-haus-casas-flotantes_2194.html',
        ]);
    }

    /** Añade el sitio solo si no hay ya uno con un nombre parecido en la guía. */
    private function anadir(string $nombre, string $parecido, string $municipio, array $campos): void
    {
        if (Sitio::where('nombre', 'like', '%'.$parecido.'%')->exists()) {
            Log::info("Alojamientos 4/11: «{$nombre}» ya está en la guía; no se toca.");

            return;
        }

        DatosGuia::crearOActualizar($nombre, $municipio, [
            'tipo' => 'Alojamiento',
            'fecha_comprobacion' => '2026-11-04',
            'verificado' => false,
            'fuente_oficial' => false,
            ...$campos,
        ]);
    }

    public function down(): void
    {
        // No se borra nada: si sobra alguno, se oculta desde el panel
    }
};
