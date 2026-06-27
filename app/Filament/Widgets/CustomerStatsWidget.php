<?php

namespace App\Filament\Widgets;

use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CustomerStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $data = Cache::remember('customer_stats', 60, function () {
            $result = DB::table('customers')
                ->selectRaw("
                    COUNT(*) as total,
                    SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active,
                    SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as inactive
                ")
                ->first();

            return [(int) $result->total, (int) $result->active, (int) $result->inactive];
        });

        [$total, $active, $inactive] = $data;

        return [
            Stat::make('Total Customers', $total)
                ->icon(Heroicon::OutlinedUsers)
                ->color('info'),
            Stat::make('Active Customers', $active)
                ->icon(Heroicon::OutlinedCheckCircle)
                ->color('success'),
            Stat::make('Inactive Customers', $inactive)
                ->icon(Heroicon::OutlinedXCircle)
                ->color('danger'),
        ];
    }
}
