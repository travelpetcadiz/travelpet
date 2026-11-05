// Livewire y Alpine se cargan desde aquí (con @livewireScriptConfig en el layout)
// para que los componentes de Alpine del mapa estén registrados antes de arrancar.
import { Livewire } from '../../vendor/livewire/livewire/dist/livewire.esm';
import './mapa';
import './viaje';

Livewire.start();

// Para el botón «Añadir a mi móvil» (Android): Chrome avisa una sola vez al cargar la web
window.addEventListener('beforeinstallprompt', e => {
    e.preventDefault();
    window.tpInstalar = e;
});
