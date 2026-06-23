<?php

namespace App\Filament\Resources\Expenses\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ExpenseInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextEntry::make('category.name')
                    ->label('Category'),
                TextEntry::make('amount')
                    ->money('USD'),
                TextEntry::make('expense_date')
                    ->label('Date')
                    ->date('M d, Y'),
                TextEntry::make('note')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->label('Created Date')
                    ->dateTime('M d, Y'),
                TextEntry::make('updated_at')
                    ->label('Updated Date')
                    ->dateTime('M d, Y'),
            ]);
    }
}
