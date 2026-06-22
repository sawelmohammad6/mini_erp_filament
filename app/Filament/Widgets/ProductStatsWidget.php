<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProductStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Products', Product::count())
                ->icon(Heroicon::OutlinedCube)
                ->color('info'),
            Stat::make('Active Products', Product::where('status', true)->count())
                ->icon(Heroicon::OutlinedCheckCircle)
                ->color('success'),
            Stat::make('Low Stock Products', Product::where('stock_quantity', '<', 10)->count())
                ->icon(Heroicon::OutlinedExclamationTriangle)
                ->color('warning'),
        ];
    }
}
