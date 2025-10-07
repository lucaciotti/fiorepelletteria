<?php

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\Pages;
use Filament\PanelProvider;
use App\Providers\Filament\Traits\HasCorePanel;

class AppPanelProvider extends PanelProvider
{
    use HasCorePanel;
    
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('app')
            ->path('')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets');
    }
}
