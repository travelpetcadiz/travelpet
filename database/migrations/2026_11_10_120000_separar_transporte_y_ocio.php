<?php

use App\Models\Sitio;
use App\Support\DatosGuia;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * «Transporte y ocio» se separa en tres categorías: Planes (Ocio), Transporte y Comercios.
     * Cada sitio va a una según su subtipo; lo que no es transporte ni comercio, a Planes.
     * No se borra nada: solo cambia el campo «tipo», y queda en el historial con fuente «GitHub».
     */
    private const TRANSPORTE = ['autobus', 'bus', 'barco', 'ferry', 'tren', 'taxi', 'avion', 'aeropuerto', 'estacion', 'lancha', 'catamaran'];

    private const COMERCIO = ['comercio', 'tienda', 'libreria', 'centro comercial', 'supermercado', 'mercado'];

    public function up(): void
    {
        if (! Sitio::exists()) {
            return;
        }

        $antes = Sitio::$origenCambios;
        Sitio::$origenCambios = DatosGuia::ORIGEN;

        try {
            Sitio::where('tipo', 'Transporte y ocio')->each(function (Sitio $sitio) {
                $sitio->update(['tipo' => static::tipoNuevo($sitio->subtipo)]);
            });
        } finally {
            Sitio::$origenCambios = $antes;
        }
    }

    public static function tipoNuevo(?string $subtipo): string
    {
        $s = Str::of((string) $subtipo)->ascii()->lower()->trim()->toString();

        foreach (self::TRANSPORTE as $palabra) {
            if (preg_match('/\b'.preg_quote($palabra, '/').'\b/', $s)) {
                return 'Transporte';
            }
        }

        foreach (self::COMERCIO as $palabra) {
            if (preg_match('/\b'.preg_quote($palabra, '/').'\b/', $s)) {
                return 'Comercio';
            }
        }

        return 'Ocio';
    }

    public function down(): void
    {
        //
    }
};
