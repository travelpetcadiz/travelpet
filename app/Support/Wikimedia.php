<?php

namespace App\Support;

use App\Models\Foto;
use App\Models\Sitio;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Fotos con licencia libre de Wikimedia Commons: buscarlas para un sitio, leer su autor y licencia
 * y guardar una copia en nuestro servidor (no se enlaza la de Commons).
 */
class Wikimedia
{
    private const API = 'https://commons.wikimedia.org/w/api.php';

    // Palabras que no sirven para saber si una foto es de ese sitio
    private const VACIAS = ['de', 'del', 'la', 'las', 'los', 'el', 'y', 'en', 'playa', 'playas', 'parque', 'canino', 'canina',
        'zona', 'area', 'hotel', 'bar', 'restaurante', 'clinica', 'veterinaria', 'centro', 'casa', 'municipal', 'normativa', 'cafe'];

    private static function http()
    {
        return Http::withHeaders(['User-Agent' => 'TravelPet/1.0 (+https://travelpetcadiz.es)'])->timeout(20)->retry(2, 1000);
    }

    /**
     * Datos de una foto a partir de su enlace de Commons
     * (https://commons.wikimedia.org/wiki/File:…).
     *
     * @return array{titulo: string, autor: ?string, licencia: ?string, licencia_url: ?string, fuente_url: string, imagen_url: string}|null
     */
    public static function datos(string $enlace): ?array
    {
        if (! preg_match('#commons\.wikimedia\.org/wiki/(File:[^?\#]+)#i', $enlace, $m)) {
            return null;
        }

        return static::info([rawurldecode(str_replace('_', ' ', $m[1]))])[0] ?? null;
    }

    /**
     * Fotos candidatas para un sitio: buscan su nombre y su pueblo, y el título de la foto
     * tiene que contener las palabras que lo distinguen (si no, salen fotos de otra cosa).
     */
    public static function buscar(Sitio $sitio, int $max = 3): array
    {
        $nombre = trim(preg_replace('/\(.*?\)/', '', $sitio->nombre));
        $claves = collect(preg_split('/[^a-z0-9]+/', Str::ascii(mb_strtolower($nombre))))
            ->filter(fn ($w) => strlen($w) > 2 && ! in_array($w, self::VACIAS, true))
            ->take(2);
        if ($claves->isEmpty()) {
            return [];
        }

        $titulos = collect(static::http()->get(self::API, [
            'action' => 'query', 'list' => 'search', 'srnamespace' => 6, 'srlimit' => 15, 'format' => 'json',
            'srsearch' => $nombre.' '.$sitio->municipio->nombre,
        ])->json('query.search', []))
            ->pluck('title')
            ->filter(fn ($t) => preg_match('/\.(jpe?g|png|webp)$/i', $t) && ! preg_match('/\b(mapa|map|plano|escudo|logo)\b/i', $t))
            ->filter(fn ($t) => $claves->every(fn ($k) => str_contains(Str::ascii(mb_strtolower($t)), $k)))
            ->take($max)
            ->values()
            ->all();

        return $titulos ? static::info($titulos) : [];
    }

    // Autor, licencia y enlaces de varias fotos de una vez
    private static function info(array $titulos): array
    {
        $paginas = static::http()->get(self::API, [
            'action' => 'query', 'prop' => 'imageinfo', 'iiprop' => 'url|extmetadata|mime', 'iiurlwidth' => 1600,
            'format' => 'json', 'titles' => implode('|', $titulos),
        ])->json('query.pages', []);

        return collect($paginas)
            ->map(function ($p) {
                $i = $p['imageinfo'][0] ?? null;
                if (! $i || ! str_starts_with($i['mime'] ?? '', 'image/')) {
                    return null;
                }
                $meta = fn ($k) => trim(html_entity_decode(strip_tags($i['extmetadata'][$k]['value'] ?? ''))) ?: null;

                return [
                    'titulo' => preg_replace('/^File:|\.\w+$/', '', $p['title']),
                    'autor' => $meta('Artist'),
                    'licencia' => $meta('LicenseShortName'),
                    'licencia_url' => $meta('LicenseUrl'),
                    'fuente_url' => $i['descriptionurl'],
                    'imagen_url' => $i['thumburl'] ?? $i['url'],
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    // Descarga la imagen a nuestro disco público; devuelve la ruta guardada
    public static function descargar(string $imagenUrl): ?string
    {
        $respuesta = static::http()->get($imagenUrl);
        if (! $respuesta->successful() || ! str_starts_with($respuesta->header('Content-Type'), 'image/')) {
            return null;
        }
        $ruta = 'fotos/commons-'.Str::random(12).'.'.(Str::after($respuesta->header('Content-Type'), 'image/') === 'png' ? 'png' : 'jpg');
        Storage::disk('public')->put($ruta, $respuesta->body());

        return $ruta;
    }

    /**
     * Crea fotos candidatas (sin publicar) para un sitio. Devuelve cuántas nuevas.
     * No repite fotos que ya tenga ese sitio.
     */
    public static function crearCandidatas(Sitio $sitio, int $max = 3): int
    {
        $ya = $sitio->fotos()->whereNotNull('fuente_url')->pluck('fuente_url')->all();
        $nuevas = 0;
        foreach (static::buscar($sitio, $max) as $d) {
            if (in_array($d['fuente_url'], $ya, true) || ! ($ruta = static::descargar($d['imagen_url']))) {
                continue;
            }
            $sitio->fotos()->create([
                'archivo' => $ruta,
                'descripcion' => $d['titulo'],
                'lugar' => $sitio->municipio->nombre,
                'autor' => $d['autor'],
                'origen' => 'wikimedia',
                'fuente_url' => $d['fuente_url'],
                'licencia' => $d['licencia'],
                'licencia_url' => $d['licencia_url'],
                'publicada' => false,
            ]);
            $nuevas++;
        }

        return $nuevas;
    }
}
