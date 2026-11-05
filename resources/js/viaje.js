// Mi viaje: los sitios que alguien quiere visitar («quiero») o ya ha visitado («visitado»).
// Sin cuenta se guardan en este navegador; con cuenta, en el servidor (y lo guardado
// antes de entrar se pasa a la cuenta). Los datos de partida van en <script id="tp-viaje">.
const CLAVE = 'travelpet-viaje';

const leerLocal = () => {
    try {
        const datos = JSON.parse(localStorage.getItem(CLAVE) || '{}');
        return datos && typeof datos === 'object' && ! Array.isArray(datos) ? datos : {};
    } catch {
        return {};
    }
};

const guardarLocal = items => {
    try {
        Object.keys(items).length ? localStorage.setItem(CLAVE, JSON.stringify(items)) : localStorage.removeItem(CLAVE);
    } catch {
        // Navegación privada o almacenamiento bloqueado: se queda solo en esta visita
    }
};

const leerConfig = () => {
    try {
        return JSON.parse(document.getElementById('tp-viaje')?.textContent || '{}');
    } catch {
        return {};
    }
};

const enviar = (url, cuerpo) => fetch(url, {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
    },
    body: JSON.stringify(cuerpo),
}).then(r => (r.ok ? r.json() : Promise.reject(r)));

document.addEventListener('alpine:init', () => {
    window.Alpine.store('viaje', {
        cuenta: false,
        url: '',
        items: {},
        aviso: '',
        reloj: null,

        // Se vuelve a leer en cada página, por si la persona acaba de entrar o salir
        cargar() {
            const cfg = leerConfig();
            this.cuenta = !! cfg.cuenta;
            this.url = cfg.url || '';
            if (! this.cuenta) {
                this.items = leerLocal();

                return;
            }
            this.items = cfg.items || {};
            // Lo guardado sin cuenta pasa a la cuenta al entrar
            const local = leerLocal();
            if (Object.keys(local).length && this.url) {
                enviar(this.url, { items: local })
                    .then(datos => { this.items = datos.items; guardarLocal({}); })
                    .catch(() => {});
            }
        },

        estado(id) {
            return this.items[id] ?? null;
        },

        get total() {
            return Object.keys(this.items).length;
        },

        // Pulsar el mismo botón otra vez lo quita
        alternar(id, estado) {
            const nuevo = this.items[id] === estado ? null : estado;
            const antes = this.items;
            const items = { ...this.items };
            nuevo ? (items[id] = nuevo) : delete items[id];
            this.items = items;
            this.avisar(nuevo === 'quiero' ? 'Guardado en Mi viaje' : nuevo === 'visitado' ? 'Apuntado en Mi viaje' : 'Quitado de Mi viaje');

            if (! this.cuenta) {
                guardarLocal(items);

                return Promise.resolve();
            }

            return enviar(this.url, { items: { [id]: nuevo } })
                .then(datos => { this.items = datos.items; })
                .catch(() => { this.items = antes; this.avisar('No se ha podido guardar. Prueba otra vez.'); });
        },

        avisar(texto) {
            this.aviso = texto;
            clearTimeout(this.reloj);
            this.reloj = setTimeout(() => { this.aviso = ''; }, 4000);
        },
    });

    window.Alpine.store('viaje').cargar();
});

document.addEventListener('livewire:navigated', () => window.Alpine?.store('viaje')?.cargar());
