<?php

namespace App\Filament\Resources\Enquiries\Schemas;

use App\Models\Enquiry;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class EnquiryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')->required(),
                TextInput::make('phone')->tel()->required(),
                TextInput::make('email')->email(),
                TextInput::make('city'),
                Select::make('service_category_id')->label('Service')->relationship('category', 'name'),
                Select::make('status')->options(Enquiry::STATUSES)->default('new')->required(),
                Textarea::make('message')->rows(5)->required()->columnSpanFull(),
            ]);
    }
}
