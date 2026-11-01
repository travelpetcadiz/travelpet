<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\Pendientes;
use App\Filament\Widgets\ResumenStats;
use Filament\Pages\Dashboard;
use Illuminate\Contracts\Support\Htmlable;

class Inicio extends Dashboard
{
    protected static ?string $navigationLabel = 'Inicio';

    protected static ?int $navigationSort = -10;

    public function getTitle(): string|Htmlable
    {
        return 'Hola, '.str(auth()->user()->name)->before(' ');
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Este es el panel de TravelPet. Aquí ves lo que tienes pendiente y cambias todo lo que sale en la web.';
    }

    public function getWidgets(): array
    {
        return [Pendientes::class, ResumenStats::class];
    }

    public function getColumns(): int|array
    {
        return 1;
    }
}
