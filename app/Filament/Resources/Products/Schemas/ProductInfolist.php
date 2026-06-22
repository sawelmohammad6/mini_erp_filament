<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ProductInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextEntry::make('name')
                    ->label('Product Name'),
                TextEntry::make('sku'),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('price')
                    ->money('USD'),
                TextEntry::make('stock_quantity')
                    ->label('Stock Quantity'),
                ImageEntry::make('image')
                    ->disk('public')
                    ->height(150),
                IconEntry::make('status')
                    ->label('Active')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger'),
                TextEntry::make('created_at')
                    ->label('Created Date')
                    ->dateTime('M d, Y'),
                TextEntry::make('updated_at')
                    ->label('Updated Date')
                    ->dateTime('M d, Y'),
            ]);
    }
}
