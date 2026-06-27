<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\CustomerStatsWidget;
use App\Filament\Widgets\DashboardAnalyticsWidget;
use App\Filament\Widgets\ExpenseStatsWidget;
use App\Filament\Widgets\MonthlySalesChartWidget;
use App\Filament\Widgets\OrderStatsWidget;
use App\Filament\Widgets\ProductStatsWidget;
use App\Filament\Widgets\QuickNotificationsWidget;
use App\Filament\Widgets\RecentActivitiesWidget;
use App\Filament\Widgets\RecentExpensesWidget;
use App\Filament\Widgets\RecentOrdersWidget;
use App\Filament\Widgets\SalesVsExpenseChartWidget;
use App\Filament\Widgets\TopSellingProductsWidget;
use App\Filament\Widgets\UserStatsWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        return [
            QuickNotificationsWidget::class,
            DashboardAnalyticsWidget::class,
            CustomerStatsWidget::class,
            ProductStatsWidget::class,
            OrderStatsWidget::class,
            ExpenseStatsWidget::class,
            UserStatsWidget::class,
            MonthlySalesChartWidget::class,
            SalesVsExpenseChartWidget::class,
            TopSellingProductsWidget::class,
            RecentOrdersWidget::class,
            RecentExpensesWidget::class,
            RecentActivitiesWidget::class,
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
