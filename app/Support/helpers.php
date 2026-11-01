<?php

// Configuración del mapa para el JavaScript: usa el PMTiles propio si existe
function mapa_config(): array
{
    $cfg = config('travelpet.mapa');

    return [
        'pmtiles' => $cfg['pmtiles_url'] ?: (is_file(public_path($cfg['pmtiles'])) ? asset($cfg['pmtiles']) : null),
        'glyphs' => $cfg['glyphs'],
        'openfreemap' => $cfg['openfreemap'],
        'centro' => $cfg['centro'],
        'zoom' => $cfg['zoom'],
    ];
}

// Convierte "tel; https://web" en texto con enlaces seguros (solo http/https)
function enlazar(?string $texto): string
{
    return collect(preg_split('/\s*;\s*/', (string) $texto, -1, PREG_SPLIT_NO_EMPTY))
        ->map(fn (string $p) => preg_match('#^https?://#i', $p)
            ? '<a href="'.e($p).'" target="_blank" rel="noopener nofollow">'.e(preg_replace('#^www\.#i', '', parse_url($p, PHP_URL_HOST) ?: $p)).'</a>'
            : e($p))
        ->implode('<br>');
}
