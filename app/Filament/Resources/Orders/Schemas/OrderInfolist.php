<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Support\Enums\TextSize;

class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextEntry::make('order_number')
                    ->label('Order #'),
                TextEntry::make('customer.name')
                    ->label('Customer'),
                TextEntry::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'processing' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                TextEntry::make('total_amount')
                    ->money('USD')
                    ->size(TextSize::Large),
                TextEntry::make('notes')
                    ->placeholder('-')
                    ->columnSpanFull(),
                RepeatableEntry::make('items')
                    ->label('Order Items')
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('product.name')
                            ->label('Product'),
                        TextEntry::make('quantity'),
                        TextEntry::make('unit_price')
                            ->money('USD'),
                        TextEntry::make('subtotal')
                            ->money('USD'),
                    ])
                    ->columns(4),
                TextEntry::make('created_at')
                    ->label('Created Date')
                    ->dateTime('M d, Y'),
                TextEntry::make('updated_at')
                    ->label('Updated Date')
                    ->dateTime('M d, Y'),
            ]);
    }
}
