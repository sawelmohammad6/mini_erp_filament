<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatus;
use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

class MonthlySalesChartWidget extends ChartWidget
{
    protected ?string $heading = 'Monthly Sales';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $year = now()->year;

        $monthlySales = Cache::remember("monthly_sales_chart_{$year}", 300, function () use ($year) {
            return Order::where('status', OrderStatus::Completed)
                ->whereYear('created_at', $year)
                ->selectRaw("MONTH(created_at) as month, SUM(total_amount) as total")
                ->groupBy('month')
                ->pluck('total', 'month')
                ->toArray();
        });

        $data = [];
        $labels = [];

        for ($month = 1; $month <= 12; $month++) {
            $labels[] = now()->month($month)->format('M');
            $data[] = round((float) ($monthlySales[$month] ?? 0), 2);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Sales ($)',
                    'data' => $data,
                    'backgroundColor' => '#22c55e',
                    'borderColor' => '#16a34a',
                ],
            ],
            'labels' => $labels,
        ];
    }
}
