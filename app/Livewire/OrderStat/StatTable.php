<?php

namespace App\Livewire\OrderStat;

use App\Models\Customer;
use App\Models\Operator;
use App\Models\ProcessType;
use App\Models\Product;
use App\Models\WorkOrder;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\ExportBulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Livewire\Component;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use pxlrbt\FilamentExcel\Actions\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Session;
use Str;

class StatTable extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;

    protected $listeners = [
        'tableRefresh' => '$refresh',
    ];

    public function table(Table $table): Table
    {
        $groupType = '';
        if (Session::has('orderstat.form.groupType')) {
            $groupType = Session::get('orderstat.form.groupType') ?? '';
        }
        $originalGroupColumns = explode('-', $groupType);

        $records = $this->_buildRecords($originalGroupColumns);
        $columns = $this->_buildColumns($originalGroupColumns);
        // dd($records);
        $table
        ->records(fn(?string $search): Collection => $records
                ->when(
                    filled($search),
                    fn(Collection $data): Collection => $data->filter(
                        fn(array $record): bool => 
                        // (str_contains(Str::lower($record['ord_num']), Str::lower($search))) ||
                        // (str_contains(Str::lower($record['customer']), Str::lower($search))) ||
                        // (str_contains(Str::lower($record['operator']), Str::lower($search))) ||
                        // (str_contains(Str::lower($record['product']), Str::lower($search))) ||
                        (str_contains(Str::lower($record['processType']), Str::lower($search))),
                    ),
                )
        )->recordClasses(fn($record) => match ($record['lvl']) {
            1 => 'row-group-lvl-1',
            2 => 'row-group-lvl-2',
            3 => 'row-group-lvl-3',
            4 => 'row-group-lvl-4',
            5 => 'row-group-lvl-5',
            default => null,
    })
        ->columns($columns)
        ->filters([
        ], layout: FiltersLayout::Modal)->filtersTriggerAction(
            fn(Action $action) => $action
                ->button()
                ->slideOver()
                ->label(__('Filter')),
        )
        ->deferFilters(false)
        ->headerActions([
        ])
        ->recordActions([
        ])
        ->toolbarActions([
        ]);

        return $table;
    }

    public function render(): View
    {
        return view('livewire.order-stat.stat-table');
    }

    protected function _buildRecords($originalGroupColumns): Collection
    {
        $groupColumns = $originalGroupColumns;
        $records = [];
        while (count($groupColumns) > 0) {
            $records = array_merge($records, $this->_recordGroupBuilder($groupColumns, $originalGroupColumns));
            $groupColumns = array_slice($groupColumns, 0, -1);
        }
        return collect($records)->sortBy(array_merge($originalGroupColumns, ['lvl']));
    }

    protected function _recordGroupBuilder($groupColumns, $originalGroupColumns): array
    {

        $products = Session::get('orderstat.form.filter.products') ?? [];
        $customers = Session::get('orderstat.form.filter.customers') ?? [];
        $operators = Session::get('orderstat.form.filter.operators') ?? [];

        $lvl = (count($groupColumns) == count($originalGroupColumns)) ? 99 : count($groupColumns);
        $records = WorkOrder::selectRaw(implode(', ', $groupColumns) . ', ' . $lvl . ' as lvl, SUM(quantity) as quantity, SUM(total_minutes) as total_minutes, MIN(created_at) as created_at, MAX(end_at) as end_at')
            ->where('end_at', '!=', null);
        if (!empty($products)){
            $records->whereIn('product_id', $products);
        }
        if (!empty($customers)){
            $records->whereIn('customer_id', $customers);
        }
        if (!empty($operators)){
            $records->whereIn('operator_id', $operators);
        }
        $records = $records->groupBy($groupColumns)
            ->get()
            ->toArray();
        return $records;
    }

    protected function _buildColumns($originalGroupColumns): array
    {
        $groupColumns = $originalGroupColumns;
        $columnsTitle = [];
        $columnsGroup = [];
        $columnsAlways = [
            TextColumn::make('quantity')->label('Qta')
                ->numeric(),
            // ->state(function ($record) {
            //     return $record['lvl']==99 ? $record['quantity'] : '';
            // }),
            TextColumn::make('total_minutes')->label('Totale Minuti')
                ->numeric(),
            TextColumn::make('avg_minutes')->label('Media Minuti')
                ->numeric()
                ->state(function ($record) {
                    return $record['total_minutes'] ? round($record['total_minutes'] / $record['quantity'], 2) : 0;
                }),
            TextColumn::make('created_at')->label('Data Creazione')
                ->dateTime()
                ->toggleable(isToggledHiddenByDefault: true),
        ];
        $columnGroupMapTitle = [];

        $state = '';
        $totCol = count($groupColumns);
        while (count($groupColumns) > 0) {
            if (!empty($state)) $state = $state . ' -> ';
            switch ($groupColumns[0]) {
                case 'process_type_id':
                    $state = $state . 'Lavorazione';
                    break;
                case 'customer_id':
                    $state = $state . 'Cliente';
                    break;
                case 'operator_id':
                    $state = $state . 'Operatore';
                    break;
                case 'product_id':
                    $state = $state . 'Prodotto';
                    break;
                case 'ord_num':
                    $state = $state . 'Commessa';
                    break;
                default:
                    $state = $state . $groupColumns[0];
                    break;
            }
            $columnGroupMapTitle[$totCol - count($groupColumns) + 1] = $state;
            $groupColumns = array_slice($groupColumns, 1);
        }

        $columnsTitle = [
            TextColumn::make('raggruppamento')
                ->state(function ($record) use ($columnGroupMapTitle) {
                    return $record['lvl'] != 99 ? $columnGroupMapTitle[$record['lvl']] : '';
                }),
        ];

        foreach ($originalGroupColumns as $value) {
            switch ($value) {
                case 'ord_num':
                    array_push($columnsGroup, TextColumn::make('ord_num')->label('Commessa'));
                    break;
                case 'process_type_id':
                    array_push($columnsGroup, TextColumn::make('processType')->label('Lavorazione')
                        ->state(function ($record) {
                            return ProcessType::find($record['process_type_id'] ?? null)->name ?? '';
                        }));
                    break;
                case 'customer_id':
                    array_push($columnsGroup, TextColumn::make('customer')->label('Cliente')
                        ->state(function ($record) {
                            return Customer::find($record['customer_id'] ?? null)->name ?? '';
                        }));
                    break;
                case 'operator_id':
                    array_push($columnsGroup, TextColumn::make('operator')->label('Operatore')
                        ->state(function ($record) {
                            return Operator::find($record['operator_id'] ?? null)->name ?? '';
                        }));
                    break;
                case 'product_id':
                    array_push($columnsGroup, TextColumn::make('product')->label('Prodotto')
                        ->state(function ($record) {
                            return Product::find($record['product_id'] ?? null)->code ?? '';
                        }));
                    break;

                default:
                    # code...
                    break;
            }
        }

        return array_merge($columnsTitle, $columnsGroup, $columnsAlways);
    }
}
