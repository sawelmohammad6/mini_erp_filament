<?php

namespace App\Filament\Widgets;

use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ExpenseStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $data = Cache::remember('expense_stats', 60, function () {
            $today = now()->startOfDay();
            $monthStart = now()->startOfMonth();

            $result = DB::table('expenses')
                ->selectRaw("
                    COALESCE(SUM(amount), 0) as total,
                    COALESCE(SUM(CASE WHEN expense_date >= ? THEN amount ELSE 0 END), 0) as today_amount,
                    COALESCE(SUM(CASE WHEN expense_date >= ? THEN amount ELSE 0 END), 0) as month_amount
                ", [$today, $monthStart])
                ->first();

            return [
                (float) $result->total,
                (float) $result->today_amount,
                (float) $result->month_amount,
            ];
        });

        [$total, $todayExpenses, $monthExpenses] = $data;

        return [
            Stat::make('Total Expenses', '$' . number_format($total, 2))
                ->icon(Heroicon::OutlinedBanknotes)
                ->color('info'),
            Stat::make("Today's Expenses", '$' . number_format($todayExpenses, 2))
                ->icon(Heroicon::OutlinedCalendarDays)
                ->color('warning'),
            Stat::make('This Month Expenses', '$' . number_format($monthExpenses, 2))
                ->icon(Heroicon::OutlinedChartBar)
                ->color('success'),
        ];
    }
}
