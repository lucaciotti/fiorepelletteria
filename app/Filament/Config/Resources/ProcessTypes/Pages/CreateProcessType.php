<?php

namespace App\Filament\Config\Resources\ProcessTypes\Pages;

use App\Filament\Config\Resources\ProcessTypes\ProcessTypeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProcessType extends CreateRecord
{
    protected static string $resource = ProcessTypeResource::class;
}
