{{-- Usa solo componentes de Filament y estilos en línea: el panel no carga el CSS de la web --}}
<x-filament-widgets::widget>
    <div style="display:grid;gap:1.5rem">
        <x-filament::section icon="heroicon-o-check-circle" :heading="$pendientes ? 'Lo que tienes pendiente' : 'Todo al día'"
            :description="$pendientes ? 'Empieza por arriba.' : 'No hay nada urgente.'">
            <div style="display:grid;gap:1rem;grid-template-columns:repeat(auto-fit,minmax(min(15rem,100%),1fr))">
                @foreach ($tareas as $t)
                    <div style="display:flex;flex-direction:column;gap:.5rem;padding:1rem;border:1px solid rgb(0 0 0 / .08);border-radius:.75rem;{{ $t['n'] ? '' : 'opacity:.6' }}">
                        <div style="display:flex;align-items:center;gap:.5rem;font-weight:700">
                            <x-filament::icon :icon="$t['icono']" style="width:1.25rem;height:1.25rem;color:#0E7F8A" />
                            {{ $t['titulo'] }}
                            <span style="margin-left:auto;flex:none">
                                <x-filament::badge :color="$t['n'] ? 'warning' : 'success'">{{ $t['n'] }}</x-filament::badge>
                            </span>
                        </div>
                        <p style="font-size:.875rem;opacity:.8;flex:1">{{ $t['texto'] }}</p>
                        @if ($t['n'])
                            <div><x-filament::button tag="a" :href="$t['url']" size="sm">{{ $t['boton'] }}</x-filament::button></div>
                        @endif
                    </div>
                @endforeach
            </div>
        </x-filament::section>

        <x-filament::section icon="heroicon-o-bolt" heading="Accesos rápidos">
            <div style="display:flex;flex-wrap:wrap;gap:.75rem">
                @foreach ($atajos as [$icono, $texto, $url, $nueva])
                    <x-filament::button tag="a" :href="$url" :icon="$icono" color="gray" :target="$nueva ? '_blank' : null">{{ $texto }}</x-filament::button>
                @endforeach
            </div>
        </x-filament::section>
    </div>
</x-filament-widgets::widget>
