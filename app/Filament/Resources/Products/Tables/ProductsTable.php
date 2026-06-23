<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
    ->url(fn ($record) => asset('storage/' . $record->image)),
                TextColumn::make('name')
                    ->label('Product Name')
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
                    ->color(fn (int $state): string => $state < 10 ? 'danger' : 'success')
                    ->formatStateUsing(fn (int $state): string => $state < 10 ? "{$state} - Low Stock" : (string) $state),
                IconColumn::make('status')
                    ->label('Status')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger'),
                TextColumn::make('created_at')
                    ->label('Created Date')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('active')
                    ->label('Active Products')
                    ->query(fn (Builder $query): Builder => $query->where('status', true))
                    ->toggle(),
                Filter::make('inactive')
                    ->label('Inactive Products')
                    ->query(fn (Builder $query): Builder => $query->where('status', false))
                    ->toggle(),
                Filter::make('low_stock')
                    ->label('Low Stock Products')
                    ->query(fn (Builder $query): Builder => $query->where('stock_quantity', '<', 10))
                    ->toggle(),
            ])
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
