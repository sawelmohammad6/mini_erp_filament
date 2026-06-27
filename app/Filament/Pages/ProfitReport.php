<?php

namespace App\Filament\Pages;

use App\Enums\OrderStatus;
use App\Exports\ProfitExport;
use App\Models\Expense;
use App\Models\Order;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;
use UnitEnum;

class ProfitReport extends Page
{
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?string $navigationLabel = 'Profit Report';

    protected static string | UnitEnum | null $navigationGroup = 'Reports';

    protected static ?int $navigationSort = 3;

    public ?string $from = null;

    public ?string $until = null;

    public float $totalSales = 0;

    public float $totalExpenses = 0;

    public float $netProfit = 0;

    public function mount(): void
    {
        $this->calculate();
    }

    public function calculate(): void
    {
        $salesQuery = Order::where('status', OrderStatus::Completed);
        $expenseQuery = Expense::query();

        if ($this->from) {
            $salesQuery->whereDate('created_at', '>=', $this->from);
            $expenseQuery->whereDate('expense_date', '>=', $this->from);
        }
        if ($this->until) {
            $salesQuery->whereDate('created_at', '<=', $this->until);
            $expenseQuery->whereDate('expense_date', '<=', $this->until);
        }

        $this->totalSales = (float) $salesQuery->sum('total_amount');
        $this->totalExpenses = (float) $expenseQuery->sum('amount');
        $this->netProfit = $this->totalSales - $this->totalExpenses;
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Date Filter')
                    ->columns(3)
                    ->schema([
                        \Filament\Forms\Components\DatePicker::make('from')
                            ->label('From Date')
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn () => $this->calculate()),
                        \Filament\Forms\Components\DatePicker::make('until')
                            ->label('Until Date')
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn () => $this->calculate()),
                    ]),
                Section::make('Profit Summary')
                    ->columns(3)
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('totalSales')
                            ->label('Total Sales')
                            ->money('USD')
                            ->state(fn () => $this->totalSales)
                            ->color('success'),
                        \Filament\Infolists\Components\TextEntry::make('totalExpenses')
                            ->label('Total Expenses')
                            ->money('USD')
                            ->state(fn () => $this->totalExpenses)
                            ->color('danger'),
                        \Filament\Infolists\Components\TextEntry::make('netProfit')
                            ->label('Net Profit')
                            ->money('USD')
                            ->state(fn () => $this->netProfit)
                            ->color(fn () => $this->netProfit >= 0 ? 'success' : 'danger'),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_excel')
                ->label('Export Excel')
                ->icon('heroicon-o-document-arrow-down')
                ->action(fn () => $this->exportExcel()),
            Action::make('export_pdf')
                ->label('Export PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->action(fn () => $this->exportPdf()),
        ];
    }

    public function exportExcel()
    {
        $filters = array_filter(['from' => $this->from, 'until' => $this->until]);
        return Excel::download(new ProfitExport($filters), 'profit-report.xlsx');
    }

    public function exportPdf()
    {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.profit', [
            'totalSales' => $this->totalSales,
            'totalExpenses' => $this->totalExpenses,
            'netProfit' => $this->netProfit,
            'from' => $this->from,
            'until' => $this->until,
        ]);
        return response()->streamDownload(fn () => print($pdf->output()), 'profit-report.pdf');
    }
}
