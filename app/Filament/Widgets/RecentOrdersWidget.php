<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatus;
use App\Models\Order;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RecentOrdersWidget extends TableWidget
{
    protected static ?string $heading = 'Recent Orders';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Order::query()
                    ->with('customer:id,name')
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('order_number')
                    ->label('Order #'),
                TextColumn::make('customer.name')
                    ->label('Customer'),
                TextColumn::make('total_amount')
                    ->money('USD'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (OrderStatus $state): string => match ($state) {
                        OrderStatus::Pending => 'warning',
                        OrderStatus::Processing => 'info',
                        OrderStatus::Completed => 'success',
                        OrderStatus::Cancelled => 'danger',
                    }),
            ])
            ->paginated(false);
    }
}
