<?php

namespace App\Filament\Widgets;

use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ProductStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $data = Cache::remember('product_stats', 60, function () {
            $result = DB::table('products')
                ->selectRaw("
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as active,
                    SUM(CASE WHEN stock_quantity > 0 AND stock_quantity < 10 THEN 1 ELSE 0 END) as low_stock
                ")
                ->first();

            return [(int) $result->total, (int) $result->active, (int) $result->low_stock];
        });

        [$total, $active, $lowStock] = $data;

        return [
            Stat::make('Total Products', $total)
                ->icon(Heroicon::OutlinedCube)
                ->color('info'),
            Stat::make('Active Products', $active)
                ->icon(Heroicon::OutlinedCheckCircle)
                ->color('success'),
            Stat::make('Low Stock Products', $lowStock)
                ->icon(Heroicon::OutlinedExclamationTriangle)
                ->color('warning'),
        ];
    }
}
