<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

// Qué hace cada menú y cómo se hacen las tareas habituales, sin jerga
class Ayuda extends Page
{
    protected string $view = 'filament.pages.ayuda';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQuestionMarkCircle;

    protected static string|UnitEnum|null $navigationGroup = 'Enlaces';

    protected static ?string $navigationLabel = 'Ayuda';

    protected static ?string $title = 'Ayuda';

    protected static ?int $navigationSort = 0;

    public function getSubheading(): ?string
    {
        return 'Qué hace cada menú y cómo se hacen las tareas de siempre, paso a paso.';
    }
}
