<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProfitExport implements FromArray, WithHeadings
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function array(): array
    {
        $salesQuery = Order::where('status', \App\Enums\OrderStatus::Completed);
        $expenseQuery = \App\Models\Expense::query();

        if (!empty($this->filters['from'])) {
            $salesQuery->whereDate('created_at', '>=', $this->filters['from']);
            $expenseQuery->whereDate('expense_date', '>=', $this->filters['from']);
        }
        if (!empty($this->filters['until'])) {
            $salesQuery->whereDate('created_at', '<=', $this->filters['until']);
            $expenseQuery->whereDate('expense_date', '<=', $this->filters['until']);
        }

        $totalSales = (float) $salesQuery->sum('total_amount');
        $totalExpenses = (float) $expenseQuery->sum('amount');
        $netProfit = $totalSales - $totalExpenses;

        return [
            ['Metric', 'Amount'],
            ['Total Sales', $totalSales],
            ['Total Expenses', $totalExpenses],
            ['Net Profit', $netProfit],
        ];
    }

    public function headings(): array
    {
        return ['Metric', 'Amount'];
    }
}
