<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Sitios\SitioResource;
use App\Filament\Resources\Users\UserResource;
use App\Models\Sitio;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ResumenStats extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    protected static bool $isDiscovered = false;

    // Se pinta junto con la página, sin el parpadeo de carga
    protected static bool $isLazy = false;

    protected ?string $heading = 'Tu guía en números';

    protected function getStats(): array
    {
        $total = Sitio::count();
        $confirmados = Sitio::where('verificado', true)->count();
        $ocultos = Sitio::where('publicado', false)->count();

        return [
            Stat::make('Sitios en la guía', $total)
                ->description($ocultos ? "{$ocultos} ocultos (no se ven en la web)" : 'Todos se ven en la web')
                ->url(SitioResource::getUrl()),
            Stat::make('Confirmados por el propio sitio', $confirmados)
                ->description($total ? round($confirmados * 100 / $total).' % del total. Cuantos más, más confianza da la guía.' : '')
                ->color('success')
                ->url(SitioResource::getUrl('index', ['filters' => ['verificado' => ['value' => '1']]])),
            Stat::make('Personas registradas', User::where('role', 'usuario')->count())
                ->description('Pueden dejar reseñas y guardar su viaje')
                ->url(auth()->user()?->isAdmin() ? UserResource::getUrl() : null),
        ];
    }
}
