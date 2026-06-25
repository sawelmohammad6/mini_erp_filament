<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
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
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                TextInput::make('password')
                    ->password()
                    ->maxLength(255)
                    ->visible(fn (string $operation): bool => $operation === 'create')
                    ->required(fn (string $operation): bool => $operation === 'create'),
                TextInput::make('password_confirmation')
                    ->password()
                    ->maxLength(255)
                    ->visible(fn (string $operation): bool => $operation === 'create')
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->same('password'),
                Select::make('roles')
                    ->multiple()
                    ->relationship('roles', 'name')
                    ->preload(),
            ]);
    }
}
