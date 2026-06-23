<?php

namespace App\Filament\Widgets;

use App\Models\Expense;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RecentExpensesWidget extends TableWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Expense::query()
                    ->with('category')
                    ->latest('expense_date')
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('category.name')
                    ->label('Category'),
                TextColumn::make('amount')
                    ->money('USD'),
                TextColumn::make('expense_date')
                    ->label('Date')
                    ->date('M d, Y'),
                TextColumn::make('note')
                    ->limit(30)
                    ->placeholder('-'),
            ])
            ->paginated(false);
    }
}
