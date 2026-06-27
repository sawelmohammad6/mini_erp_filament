<?php

namespace App\Filament\Widgets;

use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class UserStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $data = Cache::remember('user_stats', 60, function () {
            $total = DB::table('users')->count();
            $roleCounts = DB::table('users')
                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->selectRaw("roles.name, COUNT(*) as count")
                ->whereIn('roles.name', ['Admin', 'Manager', 'Staff'])
                ->groupBy('roles.name')
                ->pluck('count', 'name');

            return [$total, $roleCounts];
        });

        [$total, $roleCounts] = $data;

        return [
            Stat::make('Total Users', $total)
                ->icon(Heroicon::OutlinedUsers)
                ->color('info'),
            Stat::make('Admins', $roleCounts['Admin'] ?? 0)
                ->icon(Heroicon::OutlinedShieldCheck)
                ->color('success'),
            Stat::make('Managers', $roleCounts['Manager'] ?? 0)
                ->icon(Heroicon::OutlinedUserGroup)
                ->color('warning'),
            Stat::make('Staff', $roleCounts['Staff'] ?? 0)
                ->icon(Heroicon::OutlinedUser)
                ->color('gray'),
        ];
    }
}
