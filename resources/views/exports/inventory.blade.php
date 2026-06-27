<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Inventory Report</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        h1 { color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f4f4f4; }
        .low { color: #d97706; }
        .out { color: #dc2626; }
        .in { color: #16a34a; }
    </style>
</head>
<body>
    <h1>Inventory Report</h1>
    <table>
        <thead>
            <tr><th>Product</th><th>SKU</th><th>Price</th><th>Stock</th><th>Status</th></tr>
        </thead>
        <tbody>
            @foreach ($products as $product)
            <tr>
                <td>{{ $product->name }}</td>
                <td>{{ $product->sku }}</td>
                <td>${{ number_format($product->price, 2) }}</td>
                <td>{{ $product->stock_quantity }}</td>
                <td class="{{ $product->stock_quantity <= 0 ? 'out' : ($product->stock_quantity < 10 ? 'low' : 'in') }}">
                    {{ $product->stock_quantity <= 0 ? 'Out of Stock' : ($product->status ? 'Active' : 'Inactive') }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
