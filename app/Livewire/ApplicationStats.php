<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Item;
use App\Models\Sale;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ApplicationStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Number of Items', Item::where('status', 'active')->count()),
            Stat::make('Number of users', Customer::count()),
            Stat::make('Number of Sales', Sale::count()),
        ];
    }
}
