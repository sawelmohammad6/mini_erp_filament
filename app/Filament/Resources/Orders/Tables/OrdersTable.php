<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Enums\OrderStatus;
use App\Models\Order;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(fn () => Order::with('customer:id,name'))
            ->columns([
                TextColumn::make('order_number')
                    ->label('Order #')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('total_amount')
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (OrderStatus $state): string => match ($state) {
                        OrderStatus::Pending => 'warning',
                        OrderStatus::Processing => 'info',
                        OrderStatus::Completed => 'success',
                        OrderStatus::Cancelled => 'danger',
                    })
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created Date')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('pending')
                    ->label('Pending')
                    ->query(fn (Builder $query): Builder => $query->where('status', OrderStatus::Pending))
                    ->toggle(),
                Filter::make('processing')
                    ->label('Processing')
                    ->query(fn (Builder $query): Builder => $query->where('status', OrderStatus::Processing))
                    ->toggle(),
                Filter::make('completed')
                    ->label('Completed')
                    ->query(fn (Builder $query): Builder => $query->where('status', OrderStatus::Completed))
                    ->toggle(),
                Filter::make('cancelled')
                    ->label('Cancelled')
                    ->query(fn (Builder $query): Builder => $query->where('status', OrderStatus::Cancelled))
                    ->toggle(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
