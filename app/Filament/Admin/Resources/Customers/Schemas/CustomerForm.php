<?php

namespace App\Filament\Admin\Resources\Customers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('subname')
                    ->required(),
                TextInput::make('tva')
                    ->required(),
                TextInput::make('localita')
                    ->required(),
                TextInput::make('indirizzo')
                    ->required(),
            ]);
    }
}
