<?php

namespace App\Providers\Filament;

use App\Filament\Auth\Login;
use App\Filament\Pages\Inicio;
use Filament\FontProviders\LocalFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Vite;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    // Turquesa de la marca (#0E7F8A).
    // Color::hex() lo aclaraba demasiado y los botones quedaban cian con letra oscura.
    private const TURQUESA = [
        50 => 'oklch(0.984 0.014 205.8)',
        100 => 'oklch(0.953 0.030 205.8)',
        200 => 'oklch(0.910 0.055 205.8)',
        300 => 'oklch(0.855 0.080 205.8)',
        // Filament 5 pinta los botones con el 400/500: también en el tono de la marca
        400 => 'oklch(0.546 0.091 205.8)',
        500 => 'oklch(0.546 0.091 205.8)',
        600 => 'oklch(0.546 0.091 205.8)',
        700 => 'oklch(0.461 0.082 205.8)',
        800 => 'oklch(0.391 0.072 205.8)',
        900 => 'oklch(0.341 0.063 205.8)',
        950 => 'oklch(0.246 0.050 205.8)',
    ];

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(Login::class)
            ->brandName(config('app.name'))
            ->brandLogo(fn () => view('filament.marca'))
            ->brandLogoHeight('2.5rem')
            ->favicon(asset('logo.png'))
            ->colors([
                'primary' => self::TURQUESA,
                'warning' => Color::hex('#EBAA3C'),
            ])
            ->font('Atkinson Hyperlegible', url: fn () => Vite::asset('resources/css/panel-fuentes.css'), provider: LocalFontProvider::class)
            ->sidebarCollapsibleOnDesktop()
            ->breadcrumbs(false)
            ->globalSearch()
            // El menú se agrupa por lo que hace Natalia, no por cómo está hecha la base de datos
            ->navigationGroups([
                NavigationGroup::make('La guía'),
                NavigationGroup::make('Lo que envía la gente'),
                NavigationGroup::make('Ajustes'),
                NavigationGroup::make('Enlaces')->collapsed(false),
            ])
            ->navigationItems([
                NavigationItem::make('Ver la web')
                    ->url('/', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-globe-europe-africa')
                    ->group('Enlaces')
                    ->sort(1),
                NavigationItem::make('Bocetos del diseño nuevo')
                    ->url('/mockup', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-device-phone-mobile')
                    ->group('Enlaces')
                    ->sort(2),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Inicio::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
