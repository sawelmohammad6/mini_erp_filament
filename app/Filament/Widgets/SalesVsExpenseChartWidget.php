<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatus;
use App\Models\Expense;
use App\Models\Order;
use Filament\Widgets\ChartWidget;

class SalesVsExpenseChartWidget extends ChartWidget
{
    protected ?string $heading = 'Sales vs Expenses';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $salesData = [];
        $expenseData = [];
        $labels = [];

        for ($month = 1; $month <= 12; $month++) {
            $labels[] = now()->month($month)->format('M');
            $salesData[] = round((float) Order::where('status', OrderStatus::Completed)
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', $month)
                ->sum('total_amount'), 2);
            $expenseData[] = round((float) Expense::whereYear('expense_date', now()->year)
                ->whereMonth('expense_date', $month)
                ->sum('amount'), 2);
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
