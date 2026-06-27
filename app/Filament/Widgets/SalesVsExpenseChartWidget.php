<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatus;
use App\Models\Expense;
use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

class SalesVsExpenseChartWidget extends ChartWidget
{
    protected ?string $heading = 'Sales vs Expenses';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $year = now()->year;

        $monthlyData = Cache::remember("sales_vs_expense_chart_{$year}", 300, function () use ($year) {
            $salesByMonth = Order::where('status', OrderStatus::Completed)
                ->whereYear('created_at', $year)
                ->selectRaw("MONTH(created_at) as month, SUM(total_amount) as total")
                ->groupBy('month')
                ->pluck('total', 'month')
                ->toArray();

            $expensesByMonth = Expense::whereYear('expense_date', $year)
                ->selectRaw("MONTH(expense_date) as month, SUM(amount) as total")
                ->groupBy('month')
                ->pluck('total', 'month')
                ->toArray();

            return [$salesByMonth, $expensesByMonth];
        });

        [$salesByMonth, $expensesByMonth] = $monthlyData;
        $salesData = [];
        $expenseData = [];
        $labels = [];

        for ($month = 1; $month <= 12; $month++) {
            $labels[] = now()->month($month)->format('M');
            $salesData[] = round((float) ($salesByMonth[$month] ?? 0), 2);
            $expenseData[] = round((float) ($expensesByMonth[$month] ?? 0), 2);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Sales',
                    'data' => $salesData,
                    'backgroundColor' => '#22c55e',
                    'borderColor' => '#16a34a',
                ],
                [
                    'label' => 'Expenses',
                    'data' => $expenseData,
                    'backgroundColor' => '#ef4444',
                    'borderColor' => '#dc2626',
                ],
            ],
            'labels' => $labels,
        ];
    }
}
