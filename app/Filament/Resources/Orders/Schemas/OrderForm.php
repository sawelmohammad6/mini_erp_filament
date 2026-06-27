<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Enums\OrderStatus;
use App\Models\Product;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Facades\Cache;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Order Details')
                    ->columns(2)
                    ->schema([
                        Select::make('customer_id')
                            ->label('Customer')
                            ->relationship('customer', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('status')
                            ->options(OrderStatus::class)
                            ->default(OrderStatus::Pending->value)
                            ->required(),
                    ]),
                Section::make('Notes')
                    ->schema([
                        Textarea::make('notes')
                            ->rows(3),
                    ]),
                Section::make('Order Items')
                    ->schema([
                        Repeater::make('items')
                            ->relationship('items')
                            ->schema([
                                Select::make('product_id')
                                    ->label('Product')
                                    ->relationship('product', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->afterStateUpdated(function (Set $set, ?string $state) {
                                        if ($state) {
                                            $product = Cache::remember("product_price_{$state}", 3600, fn () => Product::find($state, ['id', 'price']));
                                            if ($product) {
                                                $set('unit_price', $product->price);
                                            }
                                        }
                                    })
                                    ->columnSpan(3),
                                TextInput::make('quantity')
                                    ->required()
                                    ->integer()
                                    ->minValue(1)
                                    ->default(1)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        self::updateSubtotal($get, $set);
                                    })
                                    ->columnSpan(1),
                                TextInput::make('unit_price')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->prefix('$')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        self::updateSubtotal($get, $set);
                                    })
                                    ->columnSpan(1),
                                TextInput::make('subtotal')
                                    ->numeric()
                                    ->prefix('$')
                                    ->disabled()
                                    ->columnSpan(1),
                            ])
                            ->columns(6)
                            ->itemLabel(fn (array $state): ?string => null),
                    ]),
            ]);
    }

    public static function updateSubtotal(Get $get, Set $set): void
    {
        $quantity = (float) ($get('quantity') ?? 0);
        $unitPrice = (float) ($get('unit_price') ?? 0);
        $set('subtotal', number_format($quantity * $unitPrice, 2, '.', ''));
    }
}
