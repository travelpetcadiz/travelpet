<?php

namespace App\Console\Commands;

use App\Models\Sitio;
use App\Support\Wikimedia;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('travelpet:fotos-wikimedia {--tipos=Playa,Espacio : Tipos de sitio, separados por comas} {--max=3 : Fotos por sitio}')]
#[Description('Busca fotos libres en Wikimedia Commons para los sitios y las deja sin publicar para revisarlas en el panel')]
class FotosWikimedia extends Command
{
    public function handle(): int
    {
        $sitios = Sitio::publicados()
            ->whereIn('tipo', explode(',', $this->option('tipos')))
            ->where('admite_perros', '!=', 'no')
            ->whereDoesntHave('fotos')
            ->with('municipio')
            ->get();

        $total = 0;
        $this->withProgressBar($sitios, function (Sitio $sitio) use (&$total) {
            $total += Wikimedia::crearCandidatas($sitio, (int) $this->option('max'));
            usleep(300_000); // sin agobiar a Wikimedia
        });

        $this->newLine();
        $this->info("{$total} fotos candidatas guardadas sin publicar. Se revisan en el panel, en Fotos.");

        return self::SUCCESS;
    }
}
