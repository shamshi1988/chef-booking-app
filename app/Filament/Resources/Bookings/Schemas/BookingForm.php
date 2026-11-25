<?php

namespace App\Filament\Resources\Bookings\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BookingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('proposal_id')
                    ->required()
                    ->numeric(),
                \Filament\Forms\Components\Select::make('status')
                    ->options(\App\Models\Booking::STATUSES)
                    ->required()
                    ->default('confirmed'),
                \Filament\Forms\Components\Select::make('payment_status')
                    ->options(['pending' => 'Pending', 'paid' => 'Paid'])
                    ->required()
                    ->default('pending'),
                \Filament\Forms\Components\DateTimePicker::make('completed_at'),
            ]);
    }
}
