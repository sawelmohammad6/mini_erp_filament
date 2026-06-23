<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatus;
use App\Models\Order;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OrderStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Orders', Order::count())
                ->icon(Heroicon::OutlinedShoppingBag)
                ->color('info'),
            Stat::make('Pending Orders', Order::where('status', OrderStatus::Pending)->count())
                ->icon(Heroicon::OutlinedClock)
                ->color('warning'),
            Stat::make('Completed Orders', Order::where('status', OrderStatus::Completed)->count())
                ->icon(Heroicon::OutlinedCheckCircle)
                ->color('success'),
            Stat::make("Today's Orders", Order::whereDate('created_at', today())->count())
                ->icon(Heroicon::OutlinedCalendarDays)
                ->color('primary'),
        ];
    }
}
