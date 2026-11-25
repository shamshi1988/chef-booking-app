<?php

namespace App\Filament\Resources\Proposals\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProposalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Select::make('service_request_id')->label('Request #')
                    ->relationship('serviceRequest', 'id')->searchable()->required(),
                Select::make('provider_id')->label('Professional')
                    ->relationship('provider', 'name', fn ($query) => $query->providers())
                    ->searchable()->preload()->required(),
                TextInput::make('price')->required()->numeric()->prefix('₹'),
                Select::make('status')->options(['pending' => 'Pending', 'accepted' => 'Accepted', 'rejected' => 'Rejected'])
                    ->default('pending')->required(),
                Textarea::make('message')->required()->columnSpanFull(),
            ]);
    }
}
