<?php

namespace App\Exports;

use App\Models\Expense;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExpenseExport implements FromQuery, WithHeadings, WithMapping
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Expense::query()->with('category');

        if (!empty($this->filters['from'])) {
            $query->whereDate('expense_date', '>=', $this->filters['from']);
        }
        if (!empty($this->filters['until'])) {
            $query->whereDate('expense_date', '<=', $this->filters['until']);
        }

        return $query;
    }

    public function headings(): array
    {
        return ['Category', 'Amount', 'Date', 'Note'];
    }

    public function map($expense): array
    {
        return [
            $expense->category?->name,
            (float) $expense->amount,
            $expense->expense_date->format('Y-m-d'),
            $expense->note ?? '',
        ];
    }
}
