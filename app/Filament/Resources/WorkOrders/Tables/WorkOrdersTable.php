<?php

namespace App\Filament\Resources\WorkOrders\Tables;

use App\Models\Customer;
use App\Models\Operator;
use App\Models\ProcessType;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use pxlrbt\FilamentExcel\Actions\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class WorkOrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort(function (Builder $query): Builder {
                return $query
                    ->orderBy('ord_num', 'desc')
                    ->orderBy('start_at', 'desc');
            })
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
                TextColumn::make('start_at')
                    ->dateTime()
                    ->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('end_at')
                    ->dateTime()
                    ->sortable()->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('status')
                    ->label('Stato')
                    ->boolean()
                    ->width('1%'),
                TextColumn::make('total_minutes')
                    ->label('Tempo Lavorazione (min.)')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                // TextColumn::make('updated_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                DateRangeFilter::make('start_at')->label('Data inizio lavorazione'),
                DateRangeFilter::make('end_at')->label('Data inizio lavorazione'),
                SelectFilter::make('customer_id')->label('Clienti')
                    ->options(fn(): array => Customer::query()->pluck('name', 'id')->all()),
                SelectFilter::make('operator_id')->label('Operatore')
                    ->options(fn(): array => Operator::query()->pluck('name', 'id')->all()),
                SelectFilter::make('product_id')->label('Prodotto')
                    ->options(fn(): array => Product::query()->pluck('code', 'id')->all()),
                SelectFilter::make('process_type_id')->label('Lavorazione')
                    ->options(fn(): array => ProcessType::query()->pluck('code', 'id')->all()),
            ], layout: FiltersLayout::Modal)->filtersTriggerAction(
                fn(Action $action) => $action
                    ->button()
                    ->slideOver()
                    ->label(__('Filter')),
            )
            ->recordActions([
                // ViewAction::make()->slideOver(),
                EditAction::make(),

            ])
            ->toolbarActions([
                ExportAction::make()->exports([
                    ExcelExport::make('table')->fromTable(),
                ]),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
