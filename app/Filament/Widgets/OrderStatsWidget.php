<?php

namespace App\Filament\Widgets;

use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class OrderStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $data = Cache::remember('order_stats', 60, function () {
            $today = now()->startOfDay();

            $result = DB::table('orders')
                ->selectRaw("
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as today_orders
                ", [$today])
                ->first();

            return [(int) $result->total, (int) $result->pending, (int) $result->completed, (int) $result->today_orders];
        });

        [$total, $pending, $completed, $todayOrders] = $data;

        return [
            Stat::make('Total Orders', $total)
                ->icon(Heroicon::OutlinedShoppingBag)
                ->color('info'),
            Stat::make('Pending Orders', $pending)
                ->icon(Heroicon::OutlinedClock)
                ->color('warning'),
            Stat::make('Completed Orders', $completed)
                ->icon(Heroicon::OutlinedCheckCircle)
                ->color('success'),
            Stat::make("Today's Orders", $todayOrders)
                ->icon(Heroicon::OutlinedCalendarDays)
                ->color('primary'),
        ];
    }
}
