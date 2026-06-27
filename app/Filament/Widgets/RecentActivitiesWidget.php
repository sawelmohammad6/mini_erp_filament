<?php

namespace App\Filament\Widgets;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RecentActivitiesWidget extends TableWidget
{
    protected static ?string $heading = 'Recent Activities';

    protected int | string | array $columnSpan = 2;

    public function table(Table $table): Table
    {
        $records = Cache::remember('recent_activities', 120, function () {
            return DB::table('activity_logs')
                ->leftJoin('users', 'activity_logs.user_id', '=', 'users.id')
                ->select('users.name as user_name', 'activity_logs.description', 'activity_logs.created_at')
                ->latest('activity_logs.created_at')
                ->limit(10)
                ->get();
        });

        return $table
            ->query(fn () => DB::table('activity_logs')->whereRaw('1 = 0'))
            ->columns([
                TextColumn::make('user_name')
                    ->label('User'),
                TextColumn::make('description')
                    ->label('Activity')
                    ->limit(50),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->since(),
            ])
            ->paginated(false)
            ->records($records);
    }
}
