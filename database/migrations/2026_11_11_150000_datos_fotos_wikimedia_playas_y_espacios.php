<?php

use App\Models\Sitio;
use App\Support\DatosGuia;
use App\Support\Wikimedia;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Log;

// Fotos libres de Wikimedia Commons para 10 playas y espacios que no tenían ninguna.
// Natalia las revisó una a una y las aprobó el 11/11/2026. El autor y la licencia se leen
// de Commons y salen debajo de la foto, como pide la licencia.
return new class extends Migration
{
    private const FOTOS = [
        ['Playa canina de Sancti Petri (Lavaculos)', 'Chiclana de la Frontera', 'File:PLAYA_Y_CASTILLO_DE_SANCTI_PETRI,_CHICLANA_DE_LA_FRONTERA.jpg'],
        ['Zona canina de Torregorda', 'Cádiz', 'File:Torregorda-IMG20260722200951.jpg'],
        ['Playa canina de Camposoto', 'San Fernando', 'File:Playa_de_Camposoto_-_IMG_20221030_161634.jpg'],
        ['Playa del Río San Pedro', 'Puerto Real', 'File:Playa_Barriada_San_Pedro_Puerto_Real-IMG_20230430_172338.jpg'],
        ['Playa de Bolonia', 'Tarifa', 'File:Playa_Bolonia-Tarifa-IMG_20230909_150824.jpg'],
        ['Parque Natural Los Alcornocales (zonas de reserva)', 'Alcalá de los Gazules', 'File:Parque_Natural_de_los_Alcornocales.jpg'],
        ['Playa de Los Lances', 'Tarifa', 'File:Playa_de_los_Lances_(Tarifa)_(2).jpg'],
        ['Playa de Valdevaqueros', 'Tarifa', 'File:PlayaValdevaqueros2013_05.JPG'],
        ['Sendero El Pinsapar', 'Grazalema', 'File:Sendero_de_El_Pinsapar.jpg'],
        ['Pinares de Rota', 'Rota', 'File:Pinar_de_Rota.jpg'],
    ];

    public function up(): void
    {
        // Base de datos vacía (tests o instalación nueva): no hay sitios a los que poner foto
        if (! Sitio::exists()) {
            return;
        }

        foreach (self::FOTOS as [$nombre, $municipio, $archivoCommons]) {
            $enlace = 'https://commons.wikimedia.org/wiki/'.$archivoCommons;

            // Si Wikimedia no responde, ese sitio se queda sin foto, pero la web se publica igual
            try {
                $sitio = DatosGuia::buscar($nombre, $municipio);
                if (! $sitio || $sitio->fotos()->where('publicada', true)->exists() || $sitio->fotos()->where('fuente_url', $enlace)->exists()) {
                    Log::warning("Foto de Wikimedia no añadida a «{$nombre}»: el sitio no existe o ya tiene foto.");

                    continue;
                }

                $datos = Wikimedia::datos($enlace);
                $ruta = $datos ? Wikimedia::descargar($datos['imagen_url']) : null;
                if (! $ruta) {
                    Log::warning("Foto de Wikimedia no añadida a «{$nombre}»: no se pudo descargar.");

                    continue;
                }

                $sitio->fotos()->create([
                    'archivo' => $ruta,
                    'descripcion' => $datos['titulo'],
                    'lugar' => $municipio,
                    'autor' => $datos['autor'],
                    'origen' => 'wikimedia',
                    'fuente_url' => $datos['fuente_url'],
                    'licencia' => $datos['licencia'],
                    'licencia_url' => $datos['licencia_url'],
                    'sin_personas' => true,
                    'publicada' => true,
                ]);
                usleep(1_500_000);
            } catch (Throwable $e) {
                Log::warning("Foto de Wikimedia no añadida a «{$nombre}»: {$e->getMessage()}");
            }
        }
    }

    public function down(): void
    {
        // Los cambios de datos no se deshacen
    }
};
