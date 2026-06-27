<?php

namespace App\Filament\Widgets;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class TopSellingProductsWidget extends TableWidget
{
    protected static ?string $heading = 'Top Selling Products';

    public function table(Table $table): Table
    {
        $records = Cache::remember('top_selling_products', 120, function () {
            return DB::table('products')
                ->selectRaw('products.id, products.name, COALESCE(SUM(order_items.quantity), 0) as total_quantity, COALESCE(SUM(order_items.subtotal), 0) as total_revenue')
                ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
                ->groupBy('products.id')
                ->orderByDesc('total_quantity')
                ->limit(5)
                ->get();
        });

        return $table
            ->query(fn () => DB::table('products')->whereRaw('1 = 0'))
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
            ->paginated(false)
            ->records($records);
    }
}
