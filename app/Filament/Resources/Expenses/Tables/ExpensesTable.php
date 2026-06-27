<?php

namespace App\Filament\Resources\Expenses\Tables;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class ExpensesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(fn () => Expense::with('category'))
            ->columns([
                TextColumn::make('category.name')
                    ->label('Category')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount')
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('expense_date')
                    ->label('Date')
                    ->date('M d, Y')
                    ->sortable(),
                TextColumn::make('note')
                    ->limit(40)
                    ->searchable()
                    ->placeholder('-'),
            ])
            ->filters([
                SelectFilter::make('expense_category_id')
                    ->label('Category')
                    ->options(fn () => Cache::remember('expense_category_options', 3600, fn () => ExpenseCategory::pluck('name', 'id')))
                    ->searchable(),
                Filter::make('today')
                    ->label('Today')
                    ->query(fn (Builder $query): Builder => $query->whereDate('expense_date', today()))
                    ->toggle(),
                Filter::make('this_week')
                    ->label('This Week')
                    ->query(fn (Builder $query): Builder => $query->whereBetween('expense_date', [now()->startOfWeek(), now()->endOfWeek()]))
                    ->toggle(),
                Filter::make('this_month')
                    ->label('This Month')
                    ->query(fn (Builder $query): Builder => $query->whereMonth('expense_date', now()->month)->whereYear('expense_date', now()->year))
                    ->toggle(),
            ])
            ->defaultSort('expense_date', 'desc')
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
