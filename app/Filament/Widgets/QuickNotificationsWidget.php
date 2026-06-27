<?php

namespace App\Filament\Widgets;

use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class QuickNotificationsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $data = Cache::remember('quick_notifications', 60, function () {
            $products = DB::table('products')
                ->selectRaw("
                    SUM(CASE WHEN stock_quantity > 0 AND stock_quantity < 10 THEN 1 ELSE 0 END) as low_stock,
                    SUM(CASE WHEN stock_quantity <= 0 THEN 1 ELSE 0 END) as out_of_stock
                ")
                ->first();

            $pendingOrders = DB::table('orders')
                ->where('status', 'pending')
                ->count();

            return [(int) $products->low_stock, (int) $products->out_of_stock, $pendingOrders];
        });

        [$lowStockCount, $outOfStockCount, $pendingOrders] = $data;

        return [
            Stat::make('Low Stock Products', $lowStockCount)
                ->icon(Heroicon::OutlinedExclamationTriangle)
                ->color('warning')
                ->description($lowStockCount > 0 ? 'Needs attention' : 'All good'),
            Stat::make('Out of Stock', $outOfStockCount)
                ->icon(Heroicon::OutlinedXCircle)
                ->color('danger')
                ->description($outOfStockCount > 0 ? 'Needs restocking' : 'All stocked'),
            Stat::make('Pending Orders', $pendingOrders)
                ->icon(Heroicon::OutlinedClock)
                ->color('info')
                ->description($pendingOrders > 0 ? 'Requires processing' : 'No pending orders'),
        ];
    }
}
