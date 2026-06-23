<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['order_number'] = 'ORD-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
        $data['total_amount'] = collect($data['items'] ?? [])
            ->sum(fn (array $item) => ($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0));

        return $data;
    }
}
