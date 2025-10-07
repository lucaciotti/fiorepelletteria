<?php

namespace App\Providers;

use BezhanSalleh\PanelSwitch\PanelSwitch;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\ServiceProvider;
use TomatoPHP\FilamentUsers\Filament\Resources\Users\Schemas\UserForm;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        PanelSwitch::configureUsing(function (PanelSwitch $panelSwitch) {
            // $panelSwitch->modalHeading('Available Panels');
            $panelSwitch->simple();
            $panelSwitch->renderHook(PanelsRenderHook::USER_MENU_BEFORE);
            // $panelSwitch->renderHook(PanelsRenderHook::USER_MENU_AFTER);
            // $panelSwitch->modalWidth('sm');
            // $panelSwitch->icons([
            //     'app' => 'heroicon-o-square-2-stack',
            //     'config' => 'heroicon-o-star',
            // ], $asImage = false);
        });
        UserForm::register([
            Select::make('operator_id')
                ->label('Operatore di Riferimento')
                ->relationship('operator', 'name')
                ->searchable()
                ->preload()
                ->createOptionForm([
                    TextInput::make('name')
                        ->label('Nome')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('description')
                        ->label('Descrizione')
                        ->required()
                        ->maxLength(255),
                ]),
        ]);
    }
}
