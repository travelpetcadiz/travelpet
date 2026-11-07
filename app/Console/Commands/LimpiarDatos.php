<?php

namespace App\Console\Commands;

use App\Models\Sugerencia;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('travelpet:limpiar-datos')]
#[Description('Borra los datos personales que la política de privacidad dice que no guardamos más tiempo del necesario')]
class LimpiarDatos extends Command
{
    // Lo que promete la política de privacidad: el contacto de los negocios, como máximo 12 meses tras revisarlo
    public const MESES_CONTACTO_NEGOCIO = 12;

    public function handle(): int
    {
        $borrados = 0;
        Sugerencia::query()
            ->where('tipo', 'negocio')
            ->where('estado', '!=', 'pendiente')
            ->where('updated_at', '<', now()->subMonths(self::MESES_CONTACTO_NEGOCIO))
            ->where('mensaje', 'like', '%Contacto (no se publica): %')
            ->where('mensaje', 'not like', '%Contacto (no se publica): [borrado]%')
            ->each(function (Sugerencia $s) use (&$borrados) {
                $s->mensaje = preg_replace('/^Contacto \(no se publica\): .*$/mu', 'Contacto (no se publica): [borrado]', $s->mensaje);
                $s->saveQuietly(); // sin tocar la fecha: ya no hay nada más que borrar
                $borrados++;
            });

        $this->info("Contactos de negocios borrados: {$borrados}");

        return self::SUCCESS;
    }
}
