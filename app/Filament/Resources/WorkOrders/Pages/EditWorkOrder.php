<?php

namespace App\Filament\Resources\WorkOrders\Pages;

use App\Filament\Resources\WorkOrders\WorkOrderResource;
use App\Models\WorkOrdersRecordTime;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditWorkOrder extends EditRecord
{
    protected static string $resource = WorkOrderResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }

    protected function getSaveFormAction(): Action
    {
        if ($this->data['start_at'] == null) {
            return Action::make('create')
                ->label(__('filament-panels::resources/pages/create-record.form.actions.create.label'))
                ->modalDescription('Non è stata configurato "Inizio Lavorazione"! Proseguire?')
                ->requiresConfirmation()
                ->action(fn() => $this->save())
                ->keyBindings(['mod+s']);
        }
        if ($this->data['end_at'] == null) {
            return Action::make('create')
                ->label(__('filament-panels::resources/pages/create-record.form.actions.create.label'))
                ->modalDescription('Non è stata configurato "Fine Lavorazione"! Proseguire?')
                ->requiresConfirmation()
                ->action(fn() => $this->save())
                ->keyBindings(['mod+s']);
        }

        return Action::make('create')
            ->label(__('filament-panels::resources/pages/create-record.form.actions.create.label'))
            // ->requiresConfirmation()
            ->action(fn() => $this->save())
            ->keyBindings(['mod+s']);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // dd($this);  
        if ($data['end_at'] != null) {
            $total_minutes = 0;
            foreach ($this->data['recordsTime'] as $deltatime) {
                if(!empty($deltatime['total_minutes'])){
                    $total_minutes += $deltatime['total_minutes'];
                } else {
                    $total_minutes += round(Carbon::createFromDate($deltatime['start_at'])->diffInMinutes(Carbon::createFromDate($deltatime['end_at'])), 0);
                }
            }
            $data['total_minutes'] = $total_minutes;
        }
        return $data;
    }


    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
