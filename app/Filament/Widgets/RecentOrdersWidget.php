<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatus;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RecentOrdersWidget extends TableWidget
{
    protected static ?string $heading = 'Recent Orders';

    public function table(Table $table): Table
    {
        $records = Cache::remember('recent_orders', 120, function () {
            return DB::table('orders')
                ->join('customers', 'orders.customer_id', '=', 'customers.id')
                ->select('orders.order_number', 'customers.name as customer_name', 'orders.total_amount', 'orders.status', 'orders.created_at')
                ->latest('orders.created_at')
                ->limit(5)
                ->get();
        });

        return $table
            ->query(fn () => DB::table('orders')->whereRaw('1 = 0'))
            ->columns([
                TextColumn::make('order_number')
                    ->label('Order #'),
                TextColumn::make('customer_name')
                    ->label('Customer'),
                TextColumn::make('total_amount')
                    ->money('USD'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'processing' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->paginated(false)
            ->records($records);
    }
}
