<?php

namespace App\Filament\Widgets;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RecentExpensesWidget extends TableWidget
{
    public function table(Table $table): Table
    {
        $records = Cache::remember('recent_expenses', 120, function () {
            return DB::table('expenses')
                ->leftJoin('expense_categories', 'expenses.expense_category_id', '=', 'expense_categories.id')
                ->select('expense_categories.name as category_name', 'expenses.amount', 'expenses.expense_date', 'expenses.note')
                ->latest('expenses.expense_date')
                ->limit(5)
                ->get();
        });

        return $table
            ->query(fn () => DB::table('expenses')->whereRaw('1 = 0'))
            ->columns([
                TextColumn::make('category_name')
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
            ->paginated(false)
            ->records($records);
    }
}
