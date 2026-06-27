<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SalesExport implements FromQuery, WithHeadings, WithMapping
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Order::query()->with('customer');

        if (!empty($this->filters['from'])) {
            $query->whereDate('created_at', '>=', $this->filters['from']);
        }
        if (!empty($this->filters['until'])) {
            $query->whereDate('created_at', '<=', $this->filters['until']);
        }

        return $query;
    }

    public function headings(): array
    {
        return ['Order #', 'Customer', 'Total Amount', 'Status', 'Date'];
    }

    public function map($order): array
    {
        return [
            $order->order_number,
            $order->customer?->name,
            (float) $order->total_amount,
            $order->status->value,
            $order->created_at->format('Y-m-d'),
        ];
    }
}
