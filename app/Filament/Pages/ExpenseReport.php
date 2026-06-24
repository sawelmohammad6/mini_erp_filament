<?php

namespace App\Filament\Pages;

use App\Models\Expense;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class ExpenseReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $navigationLabel = 'Expense Report';

    protected static string | UnitEnum | null $navigationGroup = 'Reports';

    protected static ?int $navigationSort = 2;

    public function getTitle(): string
    {
        return 'Expense Report';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Expense::query()->with('category'))
            ->columns([
                TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable(),
                TextColumn::make('amount')
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('expense_date')
                    ->label('Date')
                    ->date('M d, Y')
                    ->sortable(),
                TextColumn::make('note')
                    ->limit(50)
                    ->placeholder('-'),
            ])
            ->filters([
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
                Filter::make('date_range')
                    ->form([
                        DatePicker::make('from'),
                        DatePicker::make('until'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['from'], fn (Builder $query, $date): Builder => $query->whereDate('expense_date', '>=', $date))
                        ->when($data['until'], fn (Builder $query, $date): Builder => $query->whereDate('expense_date', '<=', $date))
                    ),
            ])
            ->defaultSort('expense_date', 'desc')
            ->paginated([10, 25, 50]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                EmbeddedTable::make(),
            ]);
    }
}
