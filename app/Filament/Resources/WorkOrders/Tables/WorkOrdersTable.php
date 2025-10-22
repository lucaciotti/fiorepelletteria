<?php

namespace App\Filament\Resources\WorkOrders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class WorkOrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ord_num')
                    ->label('Commessa')
                    ->searchable(),
                TextColumn::make('processType.name')
                    ->label('Tipo Lavorazione')
                    ->searchable(),
                TextColumn::make('operator.name')
                    ->label('Operatore')
                    ->searchable(),
                TextColumn::make('customer.name')
                    ->label('Cliente')
                    ->searchable(),
                TextColumn::make('product.id')
                    ->label('Prodotto')
                    ->searchable(),
                TextColumn::make('quantity')
                    ->label('Qta')
                    ->numeric()
                    ->sortable(),
            // TextColumn::make('start_at')
            //     ->dateTime()
            //     ->sortable(),
            // TextColumn::make('end_at')
            //     ->dateTime()
            //     ->sortable(),
            IconColumn::make('status')
                ->label('Stato')
                ->boolean()
                ->width('1%'),
            TextColumn::make('total_minutes')
                    ->label('Ore Lavorazione')
                    ->numeric()
                    ->sortable(),
                // TextColumn::make('created_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
                // TextColumn::make('updated_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                // ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
