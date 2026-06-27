<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Product;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        return DB::transaction(function () use ($data) {
            foreach ($data['items'] ?? [] as $item) {
                $product = Product::findOrFail($item['product_id']);
                if ($product->stock_quantity < $item['quantity']) {
                    Notification::make()
                        ->title("Insufficient stock for {$product->name}")
                        ->danger()
                        ->send();
                    $this->halt();
                }
            }

            $order = parent::handleRecordCreation($data);

            foreach ($data['items'] ?? [] as $item) {
                Product::where('id', $item['product_id'])
                    ->decrement('stock_quantity', $item['quantity']);
            }

            return $order;
        });
    }
}
