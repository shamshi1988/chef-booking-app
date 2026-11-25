<?php

namespace App\Filament\Resources\ProviderProfiles\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProviderProfileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Creating: link an existing professional account.
                Section::make('User account')
                    ->columnSpanFull()
                    ->visibleOn('create')
                    ->schema([
                        Select::make('user_id')
                            ->label('Professional account')
                            ->relationship('user', 'name', fn ($query) => $query->providers())
                            ->searchable(['name', 'email', 'phone'])
                            ->preload()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->helperText('Users who registered as professionals'),
                    ]),

                // Editing: the registration details themselves.
                Section::make('Account details')
                    ->description('Name, email and mobile number the professional registered with')
                    ->icon('heroicon-o-identification')
                    ->columns(3)
                    ->columnSpanFull()
                    ->visibleOn('edit')
                    ->relationship('user')
                    ->schema([
                        TextInput::make('name')->label('Full name')->required()->maxLength(255),
                        TextInput::make('email')->email()->required()->unique('users', 'email', ignoreRecord: true),
                        TextInput::make('phone')->label('Mobile number')->tel()
                            ->regex('/^(?:\+91|0)?[6-9]\d{9}$/')
                            ->validationMessages(['regex' => 'Enter a valid 10-digit Indian mobile number.']),
                    ]),

                Section::make('Professional profile')
                    ->icon('heroicon-o-wrench-screwdriver')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Select::make('service_category_id')
                            ->label('Service')
                            ->relationship('category', 'name')
                            ->preload()
                            ->required(),
                        TextInput::make('experience_years')->label('Experience (years)')->numeric()->minValue(0)->maxValue(60)->default(0)->required(),
                        TextInput::make('location')->label('City / area served'),
                        TagsInput::make('skills'),
                        Textarea::make('bio')->label('Introduction')->rows(4)->columnSpanFull(),
                        FileUpload::make('avatar_url')->label('Photo')->image()->avatar()->disk('public')->directory('avatars'),
                        Toggle::make('is_verified')->label('Verified (ID & skills checked)')->inline(false),
                    ]),
            ]);
    }
}
