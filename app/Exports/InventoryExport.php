<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class InventoryExport implements FromQuery, WithHeadings, WithMapping
{
    public function query()
    {
        return Product::query();
    }

    public function headings(): array
    {
        return ['Product Name', 'SKU', 'Price', 'Stock Quantity', 'Status'];
    }

    public function map($product): array
    {
        return [
            $product->name,
            $product->sku,
            (float) $product->price,
            $product->stock_quantity,
            $product->status ? 'Active' : 'Inactive',
        ];
    }
}
