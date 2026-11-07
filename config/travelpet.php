<?php

return [

    // Mapa. Si existe public/mapa/base.pmtiles se usa el mapa propio (Protomaps, recorte
    // de la zona que cubra la guía); si no, las teselas vectoriales gratuitas de OpenFreeMap.
    // MAPA_PMTILES_URL permite servirlo desde otro sitio (p. ej. MinIO) cuando crezca.
    'mapa' => [
        'pmtiles' => 'mapa/base.pmtiles',
        'pmtiles_url' => env('MAPA_PMTILES_URL'),
        'glyphs' => env('MAPA_GLYPHS', 'https://protomaps.github.io/basemaps-assets/fonts/{fontstack}/{range}.pbf'),
        'openfreemap' => 'https://tiles.openfreemap.org/styles/positron',
        // Vista inicial antes de ajustar el mapa a los sitios
        'centro' => [-3.7, 40.2],
        'zoom' => 5,
    ],

    // Datos del titular para el aviso legal (LSSI). Rellenar antes de publicar.
    'titular' => [
        'nombre' => env('TITULAR_NOMBRE', ''),
        'nif' => env('TITULAR_NIF', ''),
        'domicilio' => env('TITULAR_DOMICILIO', ''),
        'email' => env('TITULAR_EMAIL', 'travelpetcadiz@outlook.es'),
    ],

];
