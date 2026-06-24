<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class TopSellingProductsWidget extends TableWidget
{
    protected static ?string $heading = 'Top Selling Products';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::query()
                    ->selectRaw('products.*, COALESCE(SUM(order_items.quantity), 0) as total_quantity, COALESCE(SUM(order_items.subtotal), 0) as total_revenue')
                    ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
                    ->groupBy('products.id')
                    ->orderByDesc('total_quantity')
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Product'),
                TextColumn::make('total_quantity')
                    ->label('Qty Sold')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_revenue')
                    ->label('Revenue')
                    ->money('USD')
                    ->sortable(),
            ])
            ->paginated(false);
    }
}
