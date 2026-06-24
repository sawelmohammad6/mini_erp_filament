<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatus;
use App\Models\Order;
use Filament\Widgets\ChartWidget;

class MonthlySalesChartWidget extends ChartWidget
{
    protected ?string $heading = 'Monthly Sales';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $data = [];
        $labels = [];

        for ($month = 1; $month <= 12; $month++) {
            $labels[] = now()->month($month)->format('M');
            $total = Order::where('status', OrderStatus::Completed)
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', $month)
                ->sum('total_amount');
            $data[] = round((float) $total, 2);
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
