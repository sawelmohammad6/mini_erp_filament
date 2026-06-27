<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Profit Report</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        h1 { color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f4f4f4; }
    </style>
</head>
<body>
    <h1>Profit Report</h1>
    @if ($from) <p>From: {{ $from }} @if ($until) to {{ $until }} @endif</p> @endif
    @if ($until && !$from) <p>Until: {{ $until }}</p> @endif
    <table>
        <tr><th>Metric</th><th>Amount</th></tr>
        <tr><td>Total Sales</td><td>${{ number_format($totalSales, 2) }}</td></tr>
        <tr><td>Total Expenses</td><td>${{ number_format($totalExpenses, 2) }}</td></tr>
        <tr><td><strong>Net Profit</strong></td><td><strong>${{ number_format($netProfit, 2) }}</strong></td></tr>
    </table>
</body>
</html>
