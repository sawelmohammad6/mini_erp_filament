<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CustomerStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Customers', Customer::count())
                ->icon(Heroicon::OutlinedUsers)
                ->color('info'),
            Stat::make('Active Customers', Customer::where('is_active', true)->count())
                ->icon(Heroicon::OutlinedCheckCircle)
                ->color('success'),
            Stat::make('Inactive Customers', Customer::where('is_active', false)->count())
                ->icon(Heroicon::OutlinedXCircle)
                ->color('danger'),
        ];
    }
}
