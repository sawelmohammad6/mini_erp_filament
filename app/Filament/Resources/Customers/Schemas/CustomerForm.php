<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rule;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->email()
                    ->nullable()
                    ->unique(ignoreRecord: true),
                TextInput::make('phone')
                    ->required()
                    ->maxLength(20),
                Toggle::make('is_active')
                    ->label('Status')
                    ->default(true)
                    ->onColor('success')
                    ->offColor('danger'),
                Textarea::make('address')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
