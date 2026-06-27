<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Expense Report</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        h1 { color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f4f4f4; }
    </style>
</head>
<body>
    <h1>Expense Report</h1>
    <table>
        <thead>
            <tr><th>Category</th><th>Amount</th><th>Date</th><th>Note</th></tr>
        </thead>
        <tbody>
            @foreach ($expenses as $expense)
            <tr>
                <td>{{ $expense->category?->name }}</td>
                <td>${{ number_format($expense->amount, 2) }}</td>
                <td>{{ $expense->expense_date->format('Y-m-d') }}</td>
                <td>{{ $expense->note ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
