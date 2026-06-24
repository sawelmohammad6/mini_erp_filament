<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatus;
use App\Models\Expense;
use App\Models\Order;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardAnalyticsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalSales = Order::where('status', OrderStatus::Completed)->sum('total_amount');
        $totalExpenses = Expense::sum('amount');
        $netProfit = $totalSales - $totalExpenses;
        $totalOrders = Order::count();

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
