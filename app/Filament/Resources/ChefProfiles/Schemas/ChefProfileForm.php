<?php

namespace App\Filament\Resources\ChefProfiles\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ChefProfileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                Textarea::make('bio')
                    ->columnSpanFull(),
                TextInput::make('specialties'),
                TextInput::make('experience_years')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('location'),
                TextInput::make('avatar_url')
                    ->url(),
            ]);
    }
}
