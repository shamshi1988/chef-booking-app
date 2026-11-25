<?php

namespace App\Filament\Resources\ServiceCategories\Schemas;

use App\Models\ServiceCategory;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ServiceCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Service details')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(100)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, Get $get, ?string $state, string $operation) {
                                if ($operation === 'create' && blank($get('slug'))) {
                                    $set('slug', Str::slug((string) $state));
                                }
                            }),
                        TextInput::make('slug')
                            ->label('URL slug')
                            ->helperText('Used in the web address, e.g. /services/ac-repair')
                            ->required()
                            ->alphaDash()
                            ->unique(ignoreRecord: true)
                            ->maxLength(100),
                        TextInput::make('tagline')
                            ->helperText('One short line shown on service cards')
                            ->maxLength(150)
                            ->columnSpanFull(),
                        Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),
                        Select::make('icon')
                            ->options(ServiceCategory::ICONS)
                            ->default('home')
                            ->required(),
                        TagsInput::make('highlights')
                            ->label("What's included")
                            ->placeholder('Add a point and press Enter'),
                        TextInput::make('starting_price')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('₹')
                            ->helperText('Leave empty to show "Get a quote"'),
                        TextInput::make('price_unit')
                            ->placeholder('per visit')
                            ->maxLength(50),
                        Toggle::make('is_active')->label('Show on website')->default(true),
                        Toggle::make('is_featured')->label('Featured'),
                        TextInput::make('sort_order')->numeric()->default(0)->helperText('Lower numbers appear first'),
                    ]),

                Section::make('Booking form questions')
                    ->description('Extra questions customers answer when booking this service (date, time, address and contact details are always asked).')
                    ->columnSpanFull()
                    ->schema([
                        Repeater::make('form_fields')
                            ->hiddenLabel()
                            ->addActionLabel('Add question')
                            ->reorderable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                            ->columns(2)
                            ->schema([
                                TextInput::make('label')
                                    ->label('Question')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                                        if (blank($get('key'))) {
                                            $set('key', Str::snake(Str::limit((string) $state, 30, '')));
                                        }
                                    }),
                                TextInput::make('key')
                                    ->label('Field key')
                                    ->helperText('Internal name, letters and underscores only')
                                    ->required()
                                    ->regex('/^[a-z][a-z0-9_]*$/'),
                                Select::make('type')
                                    ->options(ServiceCategory::FIELD_TYPES)
                                    ->default('select')
                                    ->required()
                                    ->live(),
                                Toggle::make('required')->inline(false),
                                TagsInput::make('options')
                                    ->placeholder('Add an option and press Enter')
                                    ->columnSpanFull()
                                    ->required(fn (Get $get) => in_array($get('type'), ['select', 'multiselect']))
                                    ->visible(fn (Get $get) => in_array($get('type'), ['select', 'multiselect'])),
                                TextInput::make('placeholder')
                                    ->columnSpanFull()
                                    ->visible(fn (Get $get) => in_array($get('type'), ['text', 'number', 'textarea'])),
                            ]),
                    ]),
            ]);
    }
}
