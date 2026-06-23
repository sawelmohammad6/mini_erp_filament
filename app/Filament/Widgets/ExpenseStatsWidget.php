<?php

namespace App\Filament\Widgets;

use App\Models\Expense;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ExpenseStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Expenses', '$' . number_format(Expense::sum('amount'), 2))
                ->icon(Heroicon::OutlinedBanknotes)
                ->color('info'),
            Stat::make("Today's Expenses", '$' . number_format(Expense::whereDate('expense_date', today())->sum('amount'), 2))
                ->icon(Heroicon::OutlinedCalendarDays)
                ->color('warning'),
            Stat::make('This Month Expenses', '$' . number_format(Expense::whereMonth('expense_date', now()->month)->whereYear('expense_date', now()->year)->sum('amount'), 2))
                ->icon(Heroicon::OutlinedChartBar)
                ->color('success'),
        ];
    }
}
