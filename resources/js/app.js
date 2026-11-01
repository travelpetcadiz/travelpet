// Livewire y Alpine se cargan desde aquí (con @livewireScriptConfig en el layout)
// para que los componentes de Alpine del mapa estén registrados antes de arrancar.
import { Livewire } from '../../vendor/livewire/livewire/dist/livewire.esm';
import './mapa';

Livewire.start();
