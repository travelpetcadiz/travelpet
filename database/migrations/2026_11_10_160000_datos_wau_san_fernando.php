<?php

use App\Models\Sitio;
use App\Support\DatosGuia;
use Illuminate\Database\Migrations\Migration;

// Wau (peluquería y guardería canina en el C. C. Bahía Sur, San Fernando): datos que el propio negocio
// mandó a Natalia por Instagram el 10/11/2026. Abre el sábado 14/11/2026.
return new class extends Migration
{
    public function up(): void
    {
        // Base de datos vacía (tests o instalación nueva): no hay nada que añadir
        if (! Sitio::exists()) {
            return;
        }

        DatosGuia::crearOActualizar('Wau Peluquería y Guardería Canina', 'San Fernando', [
            'tipo' => 'Servicios',
            'subtipo' => 'Peluquería y guardería canina',
            'direccion' => 'Centro Comercial Bahía Sur, Av. Caño Herrera, s/n. Enfrente de Telepollo y Paris Nails',
            'latitud' => 36.4714009,
            'longitud' => -6.2090782,
            'contacto' => 'Instagram @waumascota · waumascota.com',
            'admite_perros' => 'si',
            'temporada' => 'Peluquería: de martes a sábado, de 11:00 a 14:00 y de 16:00 a 20:00. Guardería: de lunes a jueves, de 11:00 a 20:30; viernes y sábado, hasta las 21:00. Domingo cerrado, salvo algunos días de Navidad',
            'otras_condiciones' => 'Abre el sábado 14 de noviembre de 2026. Peluquería y guardería en el mismo local, dentro del centro comercial',
            'fuente' => 'El propio negocio, por Instagram (@waumascota), 10/11/2026',
            'fecha_comprobacion' => '2026-11-10',
            'verificado' => true,
            'fuente_oficial' => true,
        ], comarca: 'Bahía de Cádiz');
    }

    public function down(): void
    {
        // Los cambios de datos no se deshacen
    }
};
