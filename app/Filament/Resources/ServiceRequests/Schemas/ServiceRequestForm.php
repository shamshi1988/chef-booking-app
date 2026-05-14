<?php

namespace App\Filament\Resources\ServiceRequests\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class ServiceRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                DatePicker::make('event_date')
                    ->required(),
                TimePicker::make('event_time')
                    ->required(),
                TextInput::make('guest_count')
                    ->required()
                    ->numeric(),
                TextInput::make('budget_range_min')
                    ->numeric(),
                TextInput::make('budget_range_max')
                    ->numeric(),
                TextInput::make('cuisine_preferences'),
                Textarea::make('details')
                    ->columnSpanFull(),
                TextInput::make('status')
                    ->required()
                    ->default('open'),
            ]);
    }
}
