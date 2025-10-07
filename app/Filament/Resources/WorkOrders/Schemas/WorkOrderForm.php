<?php

namespace App\Filament\Resources\WorkOrders\Schemas;

use DateTime;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class WorkOrderForm
{
    public static function configure(Schema $schema): Schema
    {
        $operator_id = Auth::user()->operator_id;
        return $schema
            ->columns(1)
            ->components([
                TextInput::make('ord_num')
                    ->label('Commessa')
                    ->required(),
                Fieldset::make('Anagrafiche')
                    ->schema([

                        Select::make('operator_id')
                            ->default($operator_id)
                            ->label('Operatore Lavorazione')
                            ->relationship('operator', 'name')
                            ->required(),
                        Select::make('customer_id')
                            ->label('Cliente')
                            ->relationship('customer', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('Ragione Sociale')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('subname')
                                    ->label('Descrizione')
                                    ->maxLength(255),
                                TextInput::make('tva')
                                    ->label('P.Iva')
                                    ->maxLength(255),
                                TextInput::make('localita')
                                    ->label('Località')
                                    ->maxLength(255),
                                TextInput::make('indirizzo')
                                    ->label('Indirizzo')
                                    ->maxLength(255),
                            ]),
                    ]),
                Fieldset::make('Dati produzione')->columns(3)
                    ->schema([
                        Select::make('process_type_id')
                            ->label('Tipo Lavorazione')
                            ->relationship('processType', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                TextInput::make('code')
                                    ->label('Codice Lavorazione')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('name')
                                    ->label('Nome Lavorazione')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('description')
                                    ->label('Descrizione')
                                    ->maxLength(255),
                            ]),
                        Select::make('product_id')
                            ->label('Prodotto')
                            ->relationship('product', 'code')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                TextInput::make('code')
                                    ->label('Codice Prodotto')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('description')
                                    ->label('Descrizione')
                                    ->maxLength(255),
                            ]),
                        TextInput::make('quantity')
                            ->required()
                            ->numeric(),
                    ]),
                Fieldset::make('Tempi di produzione')->columns(3)
                    ->schema([
                        DateTimePicker::make('start_at')
                            ->label('Ora inizio lavorazione')
                            ->seconds(false)
                            ->readOnly(),
                        DateTimePicker::make('end_at')
                            ->label('Ora fine lavorazione')
                            ->seconds(false)
                            ->readOnly(),
                        TextInput::make('total_hours')
                            ->label('Totale ore lavorazione')
                            ->numeric(),
                    ]),
                Actions::make([
                    Action::make('Inizio Lavorazione')
                        ->icon('heroicon-m-clock')
                        ->color('success')
                        ->requiresConfirmation()
                        ->disabled(fn(Get $get) => $get('start_at') !== null)
                        ->action(function (Set $set, $state) {
                            $set('start_at', now());
                        }),
                    Action::make('Fine Lavorazione')
                        ->icon('heroicon-m-clock')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->disabled(fn(Get $get) => $get('start_at')==null)
                        ->action(function (Set $set, $state) {
                            $set('end_at', now());
                        }),
                ])->fullWidth(),
            ]);
    }
}
