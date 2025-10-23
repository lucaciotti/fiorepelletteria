<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class OrderStat extends Page
{

    protected string $view = 'filament.pages.order-stat';

    protected static string | UnitEnum | null $navigationGroup = 'Statistiche';
    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;
   
    public static ?string $title = 'Statistiche Ordini';

}
