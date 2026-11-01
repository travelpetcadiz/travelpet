<?php

namespace App\Console\Commands;

use App\Models\Cambio;
use App\Models\Municipio;
use App\Models\Provincia;
use App\Models\Sitio;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

#[Signature('travelpet:importar {archivo=database/data/datos.json} {--provincia=Cádiz} {--comunidad=Andalucía} {--si-vacio : Solo importa si no hay sitios todavía}')]
#[Description('Importa los sitios e historial de una provincia desde un datos.json como el de la web estática')]
class ImportarDatos extends Command
{
    public function handle(): int
    {
        if ($this->option('si-vacio') && Sitio::exists()) {
            $this->info('Ya hay sitios, no importo nada.');

            return self::SUCCESS;
        }

        $datos = json_decode(file_get_contents(base_path($this->argument('archivo'))), true, flags: JSON_THROW_ON_ERROR);
        $centros = $datos['config']['municipios'] ?? [];
        $ids = [];

        DB::transaction(function () use ($datos, $centros, &$ids) {
            $provincia = Provincia::firstOrCreate(
                ['nombre' => $this->option('provincia')],
                ['comunidad' => $this->option('comunidad')],
            );

            foreach ($datos['sitios'] as $r) {
                $nombreMunicipio = trim($r['Municipio']);
                $municipio = Municipio::firstOrCreate(
                    ['provincia_id' => $provincia->id, 'nombre' => $nombreMunicipio],
                    [
                        'comarca' => $this->valor($r['Comarca'] ?? null),
                        'latitud' => $centros[$nombreMunicipio][0] ?? null,
                        'longitud' => $centros[$nombreMunicipio][1] ?? null,
                    ],
                );

                $sitio = Sitio::firstOrNew(['nombre' => trim($r['Nombre']), 'municipio_id' => $municipio->id]);
                $sitio->slug ??= Sitio::slugUnico($sitio->nombre.' '.$municipio->nombre);
                // Sin eventos: la importación no debe llenar el historial
                $sitio->fill([
                    'tipo' => $r['Tipo'],
                    'subtipo' => $this->valor($r['Subtipo'] ?? null),
                    'direccion' => $this->valor($r['Dirección'] ?? null),
                    'latitud' => is_numeric($r['Latitud'] ?? null) ? (float) $r['Latitud'] : null,
                    'longitud' => is_numeric($r['Longitud'] ?? null) ? (float) $r['Longitud'] : null,
                    'contacto' => $this->valor($r['Teléfono / Web'] ?? null),
                    'admite_perros' => $this->admite($r['Admite perros (Sí/No/Con condiciones)'] ?? ''),
                    'tamano_maximo' => $this->valor($r['Tamaño o peso máximo'] ?? null),
                    'suplemento' => $this->valor($r['Suplemento (€)'] ?? null),
                    'zonas_permitidas' => $this->valor($r['Zonas permitidas'] ?? null),
                    'temporada' => $this->valor($r['Temporada'] ?? null),
                    'otras_condiciones' => $this->valor($r['Otras condiciones'] ?? null),
                    'fuente' => $this->valor($r['Fuente'] ?? null),
                    'fecha_comprobacion' => $this->valor($r['Fecha de comprobación'] ?? null),
                    'verificado' => ($r['Verificado'] ?? '') === 'Sí',
                    'fuente_oficial' => ($r['Fuente oficial'] ?? '') === 'Sí',
                ])->saveQuietly();
                $ids[$sitio->nombre.'|'.$municipio->nombre] = $sitio->id;
            }

            // Centro de la provincia: media de sus municipios
            $provincia->update([
                'latitud' => $provincia->municipios()->avg('latitud'),
                'longitud' => $provincia->municipios()->avg('longitud'),
            ]);

            // El historial solo se carga la primera vez que se importa la provincia
            if ($provincia->wasRecentlyCreated) {
                foreach ($datos['historial'] ?? [] as $h) {
                    Cambio::create([
                        'sitio_id' => $ids[trim($h['sitio']).'|'.trim($h['municipio'] ?? '')] ?? null,
                        'sitio_nombre' => $h['sitio'],
                        'municipio' => $h['municipio'] ?? null,
                        'campo' => $h['campo'],
                        'antes' => $h['antes'] ?? null,
                        'despues' => $h['despues'] ?? null,
                        'fuente' => $h['fuente'] ?? null,
                        'fecha' => $h['fecha'],
                    ]);
                }
            }
        });

        $this->info(count($ids).' sitios importados, '.Cambio::count().' cambios en el historial.');

        return self::SUCCESS;
    }

    // "No consta" y vacíos se guardan como null; la web los muestra como "No consta"
    private function valor(?string $v): ?string
    {
        $v = trim((string) $v);

        return $v === '' || mb_strtolower($v) === 'no consta' ? null : $v;
    }

    private function admite(string $v): string
    {
        return match (mb_strtolower(trim($v))) {
            'sí', 'si' => 'si',
            'con condiciones' => 'condiciones',
            'no' => 'no',
            default => 'no_consta',
        };
    }
}
