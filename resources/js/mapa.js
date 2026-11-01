// Los paquetes del mapa pesan ~1 MB: se importan solo al crear un mapa
const motor = () => import('./mapa-motor');

const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[c]);

document.addEventListener('alpine:init', () => {
    // Mapa de exploración: puntos agrupados, colores por categoría y ficha en el popup
    window.Alpine.data('mapaExplorar', (cfg, puntosIniciales) => ({
        map: null,
        geojson: null,
        listo: false,
        encuadrado: false,
        pendientes: puntosIniciales,

        async init() {
            const { maplibregl, crearMapa, geojson } = await motor();
            this.geojson = geojson;
            this.map = crearMapa(this.$refs.mapa, cfg);
            this.map.addControl(new maplibregl.GeolocateControl({ positionOptions: { enableHighAccuracy: true } }), 'top-right');

            this.map.on('load', () => {
                this.map.addSource('sitios', { type: 'geojson', data: geojson([]), cluster: true, clusterRadius: 42, clusterMaxZoom: 13 });
                this.map.addLayer({
                    id: 'grupos', type: 'circle', source: 'sitios', filter: ['has', 'point_count'],
                    paint: {
                        'circle-color': '#0E7F8A', 'circle-opacity': 0.9, 'circle-stroke-color': '#fff', 'circle-stroke-width': 2,
                        'circle-radius': ['step', ['get', 'point_count'], 16, 10, 20, 50, 26],
                    },
                });
                this.map.addLayer({
                    id: 'grupos-n', type: 'symbol', source: 'sitios', filter: ['has', 'point_count'],
                    layout: { 'text-field': ['get', 'point_count_abbreviated'], 'text-size': 13, 'text-font': cfg.pmtiles ? ['Noto Sans Medium'] : ['Noto Sans Bold'] },
                    paint: { 'text-color': '#fff' },
                });
                this.map.addLayer({
                    id: 'puntos', type: 'circle', source: 'sitios', filter: ['!', ['has', 'point_count']],
                    paint: {
                        'circle-color': ['get', 'color'], 'circle-radius': 8,
                        'circle-stroke-color': ['case', ['get', 'verificado'], '#2C7A4B', '#fff'], 'circle-stroke-width': ['case', ['get', 'verificado'], 3, 2],
                    },
                });

                this.map.on('click', 'grupos', async e => {
                    const f = e.features[0];
                    const zoom = await this.map.getSource('sitios').getClusterExpansionZoom(f.properties.cluster_id);
                    this.map.easeTo({ center: f.geometry.coordinates, zoom });
                });
                this.map.on('click', 'puntos', e => {
                    const p = e.features[0].properties;
                    new maplibregl.Popup({ offset: 10, maxWidth: '260px' })
                        .setLngLat(e.features[0].geometry.coordinates)
                        .setHTML(`<a href="${esc(p.url)}" class="block no-underline text-tinta">
                            <span class="block text-xs font-bold" style="color:${esc(p.color)}">${esc(p.tipo)}</span>
                            <strong class="block font-display text-base leading-tight">${esc(p.nombre)}</strong>
                            <span class="block text-sm text-gris">${esc(p.municipio)} · Admite: ${esc(p.admite)}</span>
                            <span class="mt-1 block text-sm font-bold text-pino">Ver ficha →</span></a>`)
                        .addTo(this.map);
                });
                for (const capa of ['grupos', 'puntos']) {
                    this.map.on('mouseenter', capa, () => (this.map.getCanvas().style.cursor = 'pointer'));
                    this.map.on('mouseleave', capa, () => (this.map.getCanvas().style.cursor = ''));
                }

                this.listo = true;
                this.pintar(this.pendientes);
            });
        },

        pintar(puntos) {
            this.pendientes = puntos;
            if (!this.listo) return;
            this.map.getSource('sitios').setData(this.geojson(puntos));
            this.encuadrar(puntos);
        },

        // Ajusta la vista a los resultados: una provincia, un pueblo o toda la guía
        encuadrar(puntos) {
            if (!puntos.length) return;
            this.map.resize(); // el contenedor puede venir de estar oculto
            const lng = puntos.map(p => p.lng), lat = puntos.map(p => p.lat);
            this.map.fitBounds(
                [[Math.min(...lng), Math.min(...lat)], [Math.max(...lng), Math.max(...lat)]],
                { padding: 50, maxZoom: 14, duration: this.encuadrado ? 600 : 0 },
            );
            this.encuadrado = true;
        },

        // Al abrir la vista de mapa el contenedor pasa de oculto a visible
        redimensionar() {
            this.$nextTick(() => this.map?.resize());
        },
    }));

    // Mapa pequeño de la ficha de un sitio
    window.Alpine.data('mapaSitio', (cfg, lng, lat, color) => ({
        async init() {
            const { maplibregl, crearMapa } = await motor();
            const map = crearMapa(this.$el, cfg, { centro: [lng, lat], zoom: 15, cooperativo: true });
            new maplibregl.Marker({ color }).setLngLat([lng, lat]).addTo(map);
        },
    }));
});
