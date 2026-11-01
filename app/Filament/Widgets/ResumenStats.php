<?php

namespace App\Filament\Widgets;

use App\Models\Resena;
use App\Models\Sitio;
use App\Models\Sugerencia;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ResumenStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $total = Sitio::count();
        $verificados = Sitio::where('verificado', true)->count();
        $sinMapa = Sitio::whereNull('latitud')->count();

        return [
            Stat::make('Sitios', $total)
                ->description(Sitio::where('publicado', false)->count().' ocultos'),
            Stat::make('Verificados', $verificados)
                ->description($total ? round($verificados * 100 / $total).' % del total' : '')
                ->color('success'),
            Stat::make('Sin coordenadas', $sinMapa)
                ->description('No salen en el mapa')
                ->color($sinMapa ? 'warning' : 'success'),
            Stat::make('Reseñas pendientes', Resena::where('estado', 'pendiente')->count())
                ->color('warning'),
            Stat::make('Sugerencias pendientes', Sugerencia::where('estado', 'pendiente')->count())
                ->color('warning'),
            Stat::make('Usuarios registrados', User::where('role', 'usuario')->count()),
        ];
    }
}
