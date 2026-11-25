<?php

namespace App\Filament\Resources\ServiceRequests\Schemas;

use App\Models\ServiceRequest;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ServiceRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Request')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Select::make('user_id')->label('Customer')->relationship('user', 'name')->searchable()->preload()->required(),
                        Select::make('service_category_id')->label('Service')->relationship('category', 'name')->preload()->required(),
                        DatePicker::make('preferred_date')->required(),
                        Select::make('preferred_time')->options(ServiceRequest::TIME_SLOTS),
                        Select::make('status')->options(ServiceRequest::STATUSES)->default('open')->required(),
                        Select::make('provider_id')->label('Requested professional')
                            ->relationship('provider', 'name', fn ($query) => $query->providers())
                            ->searchable()->preload(),
                    ]),
                Section::make('Address')
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('address')->columnSpanFull(),
                        TextInput::make('city'),
                        TextInput::make('pincode'),
                    ]),
                Section::make('Details')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        KeyValue::make('answers')->label('Answers to service questions')->columnSpanFull(),
                        TextInput::make('budget_range_min')->numeric()->prefix('₹'),
                        TextInput::make('budget_range_max')->numeric()->prefix('₹'),
                        Textarea::make('details')->rows(3)->columnSpanFull(),
                    ]),
            ]);
    }
}
