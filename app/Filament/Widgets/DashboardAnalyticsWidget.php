<?php

namespace App\Filament\Widgets;

use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardAnalyticsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $data = Cache::remember('dashboard_analytics', 60, function () {
            $result = DB::table('orders')
                ->selectRaw("
                    COALESCE(SUM(CASE WHEN status = 'completed' THEN total_amount ELSE 0 END), 0) as total_sales,
                    COUNT(*) as total_orders
                ")
                ->first();

            $totalExpenses = (float) DB::table('expenses')->sum('amount');

            return [(float) $result->total_sales, $totalExpenses, (int) $result->total_orders];
        });

        [$totalSales, $totalExpenses, $totalOrders] = $data;
        $netProfit = $totalSales - $totalExpenses;

        return [
            Stat::make('Total Sales', '$' . number_format($totalSales, 2))
                ->icon(Heroicon::OutlinedCurrencyDollar)
                ->color('success'),
            Stat::make('Total Expenses', '$' . number_format($totalExpenses, 2))
                ->icon(Heroicon::OutlinedBanknotes)
                ->color('danger'),
            Stat::make('Net Profit', '$' . number_format($netProfit, 2))
                ->icon(Heroicon::OutlinedArrowTrendingUp)
                ->color($netProfit >= 0 ? 'success' : 'danger'),
            Stat::make('Total Orders', $totalOrders)
                ->icon(Heroicon::OutlinedShoppingBag)
                ->color('info'),
        ];
    }
}
