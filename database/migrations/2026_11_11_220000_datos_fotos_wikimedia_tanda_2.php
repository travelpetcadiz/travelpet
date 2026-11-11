<?php

use App\Models\Sitio;
use App\Support\DatosGuia;
use App\Support\Wikimedia;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Log;

// Fotos libres de Wikimedia Commons para 9 sitios sin foto (tanda 2: parques y playas).
// Natalia las revisó y aprobó todas el 11/11/2026. En los parques caninos la foto es del parque donde está
// el recinto. El autor y la licencia se leen de Commons y salen debajo de la foto, como pide la licencia.
return new class extends Migration
{
    private const FOTOS = [
        ['Parque canino Laguna de Torrox', 'Jerez de la Frontera', 'File:Laguna_de_Torrox_-_P1510037.jpg'],
        ['Parque canino del Parque Laguna del Moral', 'Rota', 'File:Parque_Laguna_del_Moral_-_IMG_20211003_121718.jpg'],
        ['Parque canino de El Altillo', 'Jerez de la Frontera', 'File:Jardín_Escénico_de_El_Altillo_-_IMG_6892.jpg'],
        ['Playas de Castilnovo, El Puerco y Calas de Roche', 'Conil de la Frontera', 'File:Castilnovo_-_Conil_de_la_Frontera.jpg'],
        ['Ruta de las Calas de Roche', 'Conil de la Frontera', 'File:Roche_-_panoramio.jpg'],
        ['Zona para perros del Parque del Vino Fino', 'El Puerto de Santa María', 'File:El_Puerto_de_Santa_María_-_Parque_del_Vino_Fino_4.jpg'],
        ['Parque canino de la plaza de Santa Ana', 'Cádiz', 'File:Cadiz-IMG_20220208_162126.jpg'],
        ['Área canina Plaza Asdrúbal', 'Cádiz', 'File:Plaza_de_Asdrúbal_-_IMG_20250903_133259.jpg'],
        ['Playa canina de Los Manolos', 'La Línea de la Concepción', 'File:Playa_de_Levante._La_Línea_de_la_Concepción.jpg'],
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
