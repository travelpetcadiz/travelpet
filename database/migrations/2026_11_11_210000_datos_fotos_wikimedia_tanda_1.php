<?php

use App\Models\Sitio;
use App\Support\DatosGuia;
use App\Support\Wikimedia;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Log;

// Fotos libres de Wikimedia Commons para 9 sitios sin foto (tanda 1: playas, senderos y castillos).
// Natalia las revisó y aprobó el 11/11/2026, todas menos la del faro de Bonanza. El autor y la licencia
// se leen de Commons y salen debajo de la foto, como pide la licencia.
return new class extends Migration
{
    private const FOTOS = [
        ['Playa canina de El Aculadero', 'El Puerto de Santa María', 'File:Playa_de_El_Aculadero_(32034603912).jpg'],
        ['Playa de Zahara de los Atunes', 'Barbate', 'File:Zahara_de_los_Atunes_y_Retín.jpg'],
        ['Playas de El Palmar, Zahora y resto de Vejer', 'Vejer de la Frontera', 'File:DSCN1535s.JPG'],
        ['Faro de Camarinal y Torre de Gracia', 'Tarifa', 'File:Faro_de_Punta_Camarinal.jpg'],
        ['Castillo de Santiago', 'Sanlúcar de Barrameda', 'File:Castillo_Santiago_Sanlucar.JPG'],
        ['Visita guiada al Castillo de San Sebastián', 'Cádiz', 'File:Castillo_de_San_Sebastián_Cádiz_1.jpg'],
        ['Playa canina de Barbate (La Barra)', 'Barbate', 'File:Extremo_occidental_de_la_Playa_de_Barbate_-_panoramio.jpg'],
        ['Sendero del río Majaceite', 'El Bosque', 'File:Riomajaceite1.JPG'],
        ['Vía Verde Entre Ríos (Rota–Chipiona–Sanlúcar)', 'Chipiona', 'File:Chipiona_-_Vía_Verde_Entre_Ríos_01.jpg'],
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
