<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Product;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['total_amount'] = collect($data['items'] ?? [])
            ->sum(fn (array $item) => ($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0));

        return $data;
    }

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        return DB::transaction(function () use ($record, $data) {
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

            $oldItems = $record->items()->get();

            $record = parent::handleRecordUpdate($record, $data);

            foreach ($oldItems as $oldItem) {
                Product::where('id', $oldItem->product_id)
                    ->increment('stock_quantity', $oldItem->quantity);
            }

            foreach ($data['items'] ?? [] as $item) {
                Product::where('id', $item['product_id'])
                    ->decrement('stock_quantity', $item['quantity']);
            }

            return $record;
        });
    }
}
