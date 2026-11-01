// Motor del mapa (MapLibre + PMTiles). Se carga solo cuando hay un mapa en la página.
import * as maplibregl from 'maplibre-gl';
import 'maplibre-gl/dist/maplibre-gl.css';
// MapLibre busca su worker junto a su propio archivo; empaquetado, hay que darle la URL
import urlWorker from 'maplibre-gl/dist/maplibre-gl-worker.mjs?worker&url';
import { Protocol } from 'pmtiles';
import { layers, namedFlavor } from '@protomaps/basemaps';

maplibregl.setWorkerUrl(urlWorker);

const protocol = new Protocol();
maplibregl.addProtocol('pmtiles', protocol.tile);

// Paleta de la marca sobre el estilo claro de Protomaps
const flavor = {
    ...namedFlavor('light'),
    background: '#EEF1EC',
    earth: '#EEF1EC',
    water: '#D3EBEE',
    beach: '#F6E7C8',
    sand: '#F6E7C8',
    park_a: '#DDEBD6',
    park_b: '#D3E6CB',
    wood_a: '#D9E8D2',
    wood_b: '#CFE2C6',
    ocean_label: '#0E7F8A',
    city_label: '#132A2E',
};

function estilo(cfg) {
    if (!cfg.pmtiles) return cfg.openfreemap;

    return {
        version: 8,
        glyphs: cfg.glyphs,
        sources: {
            protomaps: {
                type: 'vector',
                url: 'pmtiles://' + cfg.pmtiles,
                attribution: '© <a href="https://openstreetmap.org/copyright">OpenStreetMap</a> · <a href="https://protomaps.com">Protomaps</a>',
            },
        },
        layers: layers('protomaps', flavor, { lang: 'es' }),
    };
}

export function crearMapa(el, cfg, opciones = {}) {
    const map = new maplibregl.Map({
        container: el,
        style: estilo(cfg),
        center: opciones.centro ?? cfg.centro,
        zoom: opciones.zoom ?? cfg.zoom,
        attributionControl: { compact: true },
        cooperativeGestures: opciones.cooperativo ?? false,
    });
    map.addControl(new maplibregl.NavigationControl({ showCompass: false }), 'top-right');

    return map;
}


export const geojson = puntos => ({
    type: 'FeatureCollection',
    features: puntos.map(p => ({
        type: 'Feature',
        geometry: { type: 'Point', coordinates: [p.lng, p.lat] },
        properties: p,
    })),
});

export { maplibregl };
