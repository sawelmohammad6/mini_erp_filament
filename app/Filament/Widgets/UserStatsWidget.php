<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Spatie\Permission\Models\Role;

class UserStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $adminRole = Role::where('name', 'Admin')->first();
        $managerRole = Role::where('name', 'Manager')->first();
        $staffRole = Role::where('name', 'Staff')->first();

        return [
            Stat::make('Total Users', User::count())
                ->icon(Heroicon::OutlinedUsers)
                ->color('info'),
            Stat::make('Admins', $adminRole ? User::role('Admin')->count() : 0)
                ->icon(Heroicon::OutlinedShieldCheck)
                ->color('success'),
            Stat::make('Managers', $managerRole ? User::role('Manager')->count() : 0)
                ->icon(Heroicon::OutlinedUserGroup)
                ->color('warning'),
            Stat::make('Staff', $staffRole ? User::role('Staff')->count() : 0)
                ->icon(Heroicon::OutlinedUser)
                ->color('gray'),
        ];
    }
}
