<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\Ayuda;
use App\Filament\Resources\Fotos\FotoResource;
use App\Filament\Resources\Resenas\ResenaResource;
use App\Filament\Resources\Sitios\SitioResource;
use App\Filament\Resources\Sugerencias\SugerenciaResource;
use App\Models\Foto;
use App\Models\Resena;
use App\Models\Sitio;
use App\Models\Sugerencia;
use Filament\Widgets\Widget;

// Inicio del panel: lo que hay que hacer, explicado, con un botón que lleva directo a ello
class Pendientes extends Widget
{
    protected string $view = 'filament.widgets.pendientes';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 1;

    protected static bool $isDiscovered = false;

    // Se pinta junto con la página, sin el parpadeo de carga
    protected static bool $isLazy = false;

    protected function getViewData(): array
    {
        $sitios = fn () => Sitio::publicados();
        $filtro = fn (string $nombre) => SitioResource::getUrl('index', ['filters' => [$nombre => ['isActive' => true]]]);

        $tareas = [
            [
                'icono' => 'heroicon-o-chat-bubble-left-right',
                'titulo' => 'Reseñas por revisar',
                'n' => Resena::where('estado', 'pendiente')->count(),
                'texto' => 'Opiniones de la gente que aún no se ven en la web. Léelas y publícalas o descártalas.',
                'boton' => 'Revisar reseñas',
                'url' => ResenaResource::getUrl(),
            ],
            [
                'icono' => 'heroicon-o-light-bulb',
                'titulo' => 'Sugerencias por revisar',
                'n' => Sugerencia::where('estado', 'pendiente')->count(),
                'texto' => 'Sitios nuevos que propone la gente y avisos de datos que están mal.',
                'boton' => 'Revisar sugerencias',
                'url' => SugerenciaResource::getUrl(),
            ],
            [
                'icono' => 'heroicon-o-clock',
                'titulo' => 'Datos que caducan pronto',
                'n' => $sitios()->where('fecha_comprobacion', '<', now()->subDays(Sitio::DIAS_ANTIGUO - 60))->count(),
                'texto' => 'Se comprobaron hace más de 4 meses. Vuelve a confirmarlos para que no salgan como «dato antiguo».',
                'boton' => 'Ver cuáles son',
                'url' => $filtro('caducan'),
            ],
            [
                'icono' => 'heroicon-o-map-pin',
                'titulo' => 'Sitios que no salen en el mapa',
                'n' => $sitios()->whereNull('latitud')->count(),
                'texto' => 'Les falta la ubicación. Búscalos en Google Maps y copia sus coordenadas.',
                'boton' => 'Ver cuáles son',
                'url' => $filtro('sin_coordenadas'),
            ],
            [
                'icono' => 'heroicon-o-calendar-days',
                'titulo' => 'Playas sin fechas',
                'n' => $sitios()->where('tipo', 'Playa')->where('admite_perros', '!=', 'no')->whereNull('periodo_tipo')->count(),
                'texto' => 'Sin fechas, la ficha no puede decir si hoy se puede ir. Añádelas cuando las sepas.',
                'boton' => 'Ver cuáles son',
                'url' => $filtro('playas_sin_fechas'),
            ],
        ];

        return [
            'tareas' => $tareas,
            'pendientes' => collect($tareas)->sum('n'),
            'atajos' => [
                ['heroicon-o-plus-circle', 'Añadir un sitio', SitioResource::getUrl('create'), false],
                ['heroicon-o-photo', Foto::exists() ? 'Subir una foto' : 'Subir tu primera foto', FotoResource::getUrl('create'), false],
                ['heroicon-o-globe-europe-africa', 'Ver la web', url('/'), true],
                ['heroicon-o-question-mark-circle', 'Ayuda: qué hace cada menú', Ayuda::getUrl(), false],
            ],
        ];
    }
}
