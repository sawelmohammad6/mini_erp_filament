<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\DashboardAnalyticsWidget;
use App\Filament\Widgets\MonthlySalesChartWidget;
use App\Filament\Widgets\RecentOrdersWidget;
use App\Filament\Widgets\SalesVsExpenseChartWidget;
use App\Filament\Widgets\TopSellingProductsWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        return [
            DashboardAnalyticsWidget::class,
            MonthlySalesChartWidget::class,
            SalesVsExpenseChartWidget::class,
            TopSellingProductsWidget::class,
            RecentOrdersWidget::class,
        ];
    }

    public function getColumns(): int | array
    {
        return [
            'md' => 2,
            'xl' => 4,
        ];
    }
}
