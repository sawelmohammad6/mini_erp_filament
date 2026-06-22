<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CustomerInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextEntry::make('name'),
                TextEntry::make('email')
                    ->placeholder('-'),
                TextEntry::make('phone'),
                TextEntry::make('address')
                    ->placeholder('-')
                    ->columnSpanFull(),
                IconEntry::make('is_active')
                    ->label('Status')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->label('Created Date')
                    ->dateTime('M d, Y'),
                TextEntry::make('updated_at')
                    ->label('Updated Date')
                    ->dateTime('M d, Y'),
            ]);
    }
}
