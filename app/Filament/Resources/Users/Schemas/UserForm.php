<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Account details')
                    ->icon('heroicon-o-identification')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')->label('Full name')->required()->maxLength(255),
                        TextInput::make('email')->label('Email address')->email()->required()
                            ->unique(ignoreRecord: true),
                        TextInput::make('phone')->label('Mobile number')->tel()
                            ->regex('/^(?:\+91|0)?[6-9]\d{9}$/')
                            ->validationMessages(['regex' => 'Enter a valid 10-digit Indian mobile number.']),
                        DateTimePicker::make('email_verified_at')->label('Email verified at'),
                        TextInput::make('password')
                            ->password()
                            ->revealable()
                            ->label(fn (string $operation) => $operation === 'create' ? 'Password' : 'New password')
                            ->helperText(fn (string $operation) => $operation === 'edit' ? 'Leave empty to keep the current password.' : null)
                            ->required(fn (string $operation) => $operation === 'create')
                            ->minLength(8)
                            ->dehydrated(fn (?string $state) => filled($state))
                            ->dehydrateStateUsing(fn (string $state) => Hash::make($state)),
                    ]),
            ]);
    }
}
