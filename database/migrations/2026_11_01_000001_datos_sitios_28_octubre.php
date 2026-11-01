<?php

use App\Models\Sitio;
use App\Support\DatosGuia;
use Illuminate\Database\Migrations\Migration;

// Sitios añadidos el 28/10/2026 que no entraron en la carga inicial de la web nueva,
// y correcciones de esos días que también se quedaron fuera.
return new class extends Migration
{
    public function up(): void
    {
        // Base de datos vacía (tests o instalación nueva): no hay nada que corregir
        if (! Sitio::exists()) {
            return;
        }

        DatosGuia::crearOActualizar('Parque Canino Picadueñas', 'Jerez de la Frontera', [
            'tipo' => 'Espacio',
            'subtipo' => 'Parque canino',
            'admite_perros' => 'si',
            'zonas_permitidas' => 'Parque para perros',
            'temporada' => 'Abierto 24 horas',
            'otras_condiciones' => 'Acceso adaptado. Normas y equipamiento: pendientes de confirmar',
            'fuente' => 'Google Maps (captura enviada por Natalia, 28/10/2026)',
            'fecha_comprobacion' => '2026-10-28',
            'verificado' => false,
            'fuente_oficial' => false,
        ], comarca: 'Campiña de Jerez');

        DatosGuia::crearOActualizar('Parque Canino San Telmo', 'Jerez de la Frontera', [
            'tipo' => 'Espacio',
            'subtipo' => 'Parque canino',
            'admite_perros' => 'si',
            'zonas_permitidas' => 'Parque para perros',
            'otras_condiciones' => 'Normas y equipamiento: pendientes de confirmar',
            'fuente' => 'Google Maps (captura enviada por Natalia, 28/10/2026)',
            'fecha_comprobacion' => '2026-10-28',
            'verificado' => false,
            'fuente_oficial' => false,
        ], comarca: 'Campiña de Jerez');

        DatosGuia::crearOActualizar('Parque canino Francisco Becerra', 'Jerez de la Frontera', [
            'tipo' => 'Espacio',
            'subtipo' => 'Parque canino',
            'admite_perros' => 'si',
            'zonas_permitidas' => 'Parque para perros',
            'temporada' => 'Abierto 24 horas',
            'otras_condiciones' => 'Normas y equipamiento: pendientes de confirmar',
            'fuente' => 'Google Maps (captura enviada por Natalia, 28/10/2026)',
            'fecha_comprobacion' => '2026-10-28',
            'verificado' => false,
            'fuente_oficial' => false,
        ], comarca: 'Campiña de Jerez');

        DatosGuia::crearOActualizar('Parque perruno Alejandro', 'Jerez de la Frontera', [
            'tipo' => 'Espacio',
            'subtipo' => 'Parque canino',
            'admite_perros' => 'si',
            'zonas_permitidas' => 'Parque para perros',
            'temporada' => 'Abierto 24 horas',
            'otras_condiciones' => 'Acceso adaptado. Normas y equipamiento: pendientes de confirmar',
            'fuente' => 'Google Maps (captura enviada por Natalia, 28/10/2026)',
            'fecha_comprobacion' => '2026-10-28',
            'verificado' => false,
            'fuente_oficial' => false,
        ], comarca: 'Campiña de Jerez');

        DatosGuia::crearOActualizar('Parque Canino Autóctono de Montealegre', 'Jerez de la Frontera', [
            'tipo' => 'Espacio',
            'subtipo' => 'Parque canino',
            'admite_perros' => 'si',
            'zonas_permitidas' => 'Parque para perros',
            'temporada' => 'Abierto 24 horas',
            'otras_condiciones' => 'Normas y equipamiento: pendientes de confirmar',
            'fuente' => 'Google Maps (captura enviada por Natalia, 28/10/2026)',
            'fecha_comprobacion' => '2026-10-28',
            'verificado' => false,
            'fuente_oficial' => false,
        ], comarca: 'Campiña de Jerez');

        DatosGuia::crearOActualizar('Parque Canino de La Marquesa «Acj. Manada»', 'Jerez de la Frontera', [
            'tipo' => 'Espacio',
            'subtipo' => 'Parque canino',
            'admite_perros' => 'si',
            'zonas_permitidas' => 'Parque para perros',
            'temporada' => 'Abierto 24 horas',
            'otras_condiciones' => 'Puede ser un recinto distinto del Parque canino de La Marquesa; pendiente de confirmar. Normas y equipamiento: pendientes de confirmar',
            'fuente' => 'Google Maps (captura enviada por Natalia, 28/10/2026)',
            'fecha_comprobacion' => '2026-10-28',
            'verificado' => false,
            'fuente_oficial' => false,
        ], comarca: 'Campiña de Jerez');

        DatosGuia::crearOActualizar('Parque canino Lola Flores', 'Jerez de la Frontera', [
            'tipo' => 'Espacio',
            'subtipo' => 'Parque canino',
            'admite_perros' => 'si',
            'zonas_permitidas' => 'Parque para perros',
            'temporada' => 'Abierto 24 horas',
            'otras_condiciones' => 'Normas y equipamiento: pendientes de confirmar',
            'fuente' => 'Google Maps (captura enviada por Natalia, 28/10/2026)',
            'fecha_comprobacion' => '2026-10-28',
            'verificado' => false,
            'fuente_oficial' => false,
        ], comarca: 'Campiña de Jerez');

        DatosGuia::crearOActualizar('Parque Canino El Árbol del Pipo', 'Jerez de la Frontera', [
            'tipo' => 'Espacio',
            'subtipo' => 'Parque canino',
            'admite_perros' => 'si',
            'zonas_permitidas' => 'Parque para perros',
            'temporada' => 'Abierto 24 horas',
            'otras_condiciones' => 'Normas y equipamiento: pendientes de confirmar',
            'fuente' => 'Google Maps (captura enviada por Natalia, 28/10/2026)',
            'fecha_comprobacion' => '2026-10-28',
            'verificado' => false,
            'fuente_oficial' => false,
        ], comarca: 'Campiña de Jerez');

        DatosGuia::crearOActualizar('Centro Veterinario Animalia', 'Jerez de la Frontera', [
            'tipo' => 'Salud',
            'subtipo' => 'Clínica veterinaria',
            'admite_perros' => 'si',
            'otras_condiciones' => 'Dirección, horario y teléfono: pendientes de confirmar',
            'fuente' => 'Google Maps (captura enviada por Natalia, 28/10/2026)',
            'fecha_comprobacion' => '2026-10-28',
            'verificado' => false,
            'fuente_oficial' => false,
        ], comarca: 'Campiña de Jerez');

        DatosGuia::crearOActualizar('Consulta Veterinaria Rocío Junquera', 'Jerez de la Frontera', [
            'tipo' => 'Salud',
            'subtipo' => 'Clínica veterinaria',
            'admite_perros' => 'si',
            'otras_condiciones' => 'Dirección, horario y teléfono: pendientes de confirmar',
            'fuente' => 'Google Maps (captura enviada por Natalia, 28/10/2026)',
            'fecha_comprobacion' => '2026-10-28',
            'verificado' => false,
            'fuente_oficial' => false,
        ], comarca: 'Campiña de Jerez');

        DatosGuia::crearOActualizar('Clínica Veterinaria La Barca', 'Jerez de la Frontera', [
            'tipo' => 'Salud',
            'subtipo' => 'Clínica veterinaria',
            'admite_perros' => 'si',
            'otras_condiciones' => 'En La Barca de la Florida (Jerez). Dirección y teléfono: pendientes de confirmar',
            'fuente' => 'Google Maps (captura enviada por Natalia, 28/10/2026)',
            'fecha_comprobacion' => '2026-10-28',
            'verificado' => false,
            'fuente_oficial' => false,
        ], comarca: 'Campiña de Jerez');

        DatosGuia::crearOActualizar('VetExotics Clínica Veterinaria', 'Jerez de la Frontera', [
            'tipo' => 'Salud',
            'subtipo' => 'Clínica veterinaria',
            'admite_perros' => 'si',
            'otras_condiciones' => 'Especializada también en animales exóticos. Dirección y teléfono: pendientes de confirmar',
            'fuente' => 'Google Maps (captura enviada por Natalia, 28/10/2026)',
            'fecha_comprobacion' => '2026-10-28',
            'verificado' => false,
            'fuente_oficial' => false,
        ], comarca: 'Campiña de Jerez');

        DatosGuia::crearOActualizar('Micasol', 'Tarifa', [
            'tipo' => 'Servicios',
            'subtipo' => 'Peluquería canina',
            'direccion' => 'C. Cigüeña Negra, 94 (Edificio Balcón de Europa), 11380 Tarifa, Cádiz',
            'admite_perros' => 'si',
            'otras_condiciones' => 'Peluquería de mascotas. Horario, precios y cita previa: pendientes de confirmar',
            'fuente' => 'Google Maps / Street View (enero 2025), indicado por Natalia el 28/10/2026',
            'fecha_comprobacion' => '2026-10-28',
            'verificado' => false,
            'fuente_oficial' => false,
        ], comarca: 'Campo de Gibraltar');

        DatosGuia::actualizar('Zootécnica Jerezana', 'Jerez de la Frontera', [
            'subtipo' => 'Clínica veterinaria · Urgencias 24 h',
            'temporada' => 'Clínica en horario habitual; servicio aparte de urgencias abierto 24 horas',
        ]);

        DatosGuia::actualizar('Na Veterinarios', 'Jerez de la Frontera', [
            'subtipo' => 'Hospital veterinario · Urgencias 24 h',
            'temporada' => 'Abierto 24 horas',
        ]);

        DatosGuia::actualizar('Centro Veterinario Los Lances', 'Tarifa', [
            'otras_condiciones' => 'DUDOSO: en esta dirección hay ahora una peluquería de mascotas (Micasol), no un veterinario. Puede haber cerrado o cambiado de local. Pendiente de confirmar',
            'fecha_comprobacion' => '2026-10-28',
        ]);
    }

    public function down(): void
    {
        // Los cambios de datos no se deshacen con rollback
    }
};
