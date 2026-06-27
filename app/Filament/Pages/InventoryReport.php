<?php

namespace App\Filament\Pages;

use App\Exports\InventoryExport;
use App\Models\Product;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;
use UnitEnum;

class InventoryReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedCube;

    protected static ?string $navigationLabel = 'Inventory Report';

    protected static string | UnitEnum | null $navigationGroup = 'Reports';

    protected static ?int $navigationSort = 4;

    public function getTitle(): string
    {
        return 'Inventory Report';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Product::query())
            ->columns([
                TextColumn::make('name')
                    ->label('Product')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('sku')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('price')
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('stock_quantity')
                    ->label('Stock')
                    ->sortable()
                    ->badge()
                    ->color(fn (int $state): string => $state <= 0 ? 'danger' : ($state < 10 ? 'warning' : 'success'))
                    ->formatStateUsing(fn (int $state): string => $state <= 0 ? 'Out of Stock' : ($state < 10 ? "{$state} - Low Stock" : (string) $state)),
                IconColumn::make('status')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger'),
            ])
            ->filters([
                Filter::make('in_stock')
                    ->label('In Stock')
                    ->query(fn (Builder $query): Builder => $query->where('stock_quantity', '>', 0))
                    ->toggle(),
                Filter::make('low_stock')
                    ->label('Low Stock')
                    ->query(fn (Builder $query): Builder => $query->where('stock_quantity', '>', 0)->where('stock_quantity', '<', 10))
                    ->toggle(),
                Filter::make('out_of_stock')
                    ->label('Out of Stock')
                    ->query(fn (Builder $query): Builder => $query->where('stock_quantity', '<=', 0))
                    ->toggle(),
            ])
            ->defaultSort('stock_quantity', 'asc')
            ->paginated([10, 25, 50]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                EmbeddedTable::make(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_excel')
                ->label('Export Excel')
                ->icon('heroicon-o-document-arrow-down')
                ->action(fn () => Excel::download(new InventoryExport(), 'inventory-report.xlsx')),
            Action::make('export_pdf')
                ->label('Export PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->action(fn () => $this->exportPdf()),
        ];
    }

    public function exportPdf()
    {
        $products = Product::all();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.inventory', ['products' => $products]);
        return response()->streamDownload(fn () => print($pdf->output()), 'inventory-report.pdf');
    }
}
