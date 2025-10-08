<?php

namespace App\Providers\Filament;

use Filament\Enums\ThemeMode;
use Filament\Panel;
use Filament\Pages;
use Filament\Widgets;
use Filament\Support\Colors\Color;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class CorePanel extends Panel
{
    protected function setUp(): void
    {
        $this
            ->viteTheme('resources/css/filament/default/theme.css')
            ->defaultThemeMode(ThemeMode::Light)
            ->brandLogo(asset('images/logo.png'))
            ->brandName('Fiore Pelletteria App')
            ->brandLogoHeight('8rem')
            ->sidebarCollapsibleOnDesktop()
            ->unsavedChangesAlerts()
            ->databaseNotifications(isLazy: true)
            ->login()
            ->colors([
                'primary' => Color::Red,
            ])
            // ->pages([
            //     Pages\Dashboard::class,
            // ])
            ->widgets([
                Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
