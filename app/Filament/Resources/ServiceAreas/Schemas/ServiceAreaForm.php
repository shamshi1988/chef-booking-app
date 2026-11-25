<?php

namespace App\Filament\Resources\ServiceAreas\Schemas;

use App\Models\PincodeDirectory;
use App\Models\ServiceArea;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class ServiceAreaForm
{
    /** Merge new PIN codes into the field, keeping it clean and sorted. */
    private static function addPincodes(Get $get, Set $set, array $new): int
    {
        $current = ServiceArea::normalisePincodes($get('pincodes') ?? []);
        $merged = ServiceArea::normalisePincodes(array_merge($current, $new));
        $set('pincodes', $merged);

        return count($merged) - count($current);
    }

    public static function configure(Schema $schema): Schema
    {
        $directoryLoaded = PincodeDirectory::isLoaded();

        return $schema
            ->components([
                Section::make('Area')
                    ->description('A region where customers can book. Customers outside all active areas cannot book.')
                    ->icon('heroicon-o-map-pin')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')->label('Area name')->placeholder('e.g. Kolkata North')->required()->maxLength(100),
                        TextInput::make('city')->placeholder('e.g. Kolkata')->required()->maxLength(100)
                            ->datalist(fn () => ServiceArea::query()->distinct()->orderBy('city')->pluck('city')->all()),
                        Toggle::make('is_active')->label('Accept bookings in this area')->default(true)->inline(false),
                    ]),

                Section::make('Fill PIN codes automatically')
                    ->description($directoryLoaded
                        ? 'Pick a district to add all of its PIN codes at once, or search for individual localities. You can remove any PIN code below.'
                        : 'The India Post PIN code directory has not been imported yet. Run "php artisan pincodes:import" (see README) to enable automatic filling.')
                    ->icon('heroicon-o-sparkles')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Select::make('pick_state')
                            ->label('State / UT')
                            ->options(fn () => PincodeDirectory::states())
                            ->searchable()
                            ->live()
                            ->dehydrated(false)
                            ->disabled(! $directoryLoaded)
                            ->afterStateUpdated(fn (Set $set) => $set('pick_district', null)),
                        Select::make('pick_district')
                            ->label('District')
                            ->options(fn (Get $get) => PincodeDirectory::districts($get('pick_state')))
                            ->searchable()
                            ->live()
                            ->dehydrated(false)
                            ->disabled(fn (Get $get) => ! $directoryLoaded || ! $get('pick_state'))
                            ->helperText(fn (Get $get) => $get('pick_state') && $get('pick_district')
                                ? count(PincodeDirectory::pincodesForDistrict($get('pick_state'), $get('pick_district'))) . ' PIN codes in this district'
                                : null),

                        Actions::make([
                            Action::make('addDistrict')
                                ->label('Add all PIN codes of this district')
                                ->icon('heroicon-o-plus-circle')
                                ->color('primary')
                                ->disabled(fn (Get $get) => ! $get('pick_state') || ! $get('pick_district'))
                                ->action(function (Get $get, Set $set) {
                                    $district = $get('pick_district');
                                    $added = self::addPincodes($get, $set, PincodeDirectory::pincodesForDistrict($get('pick_state'), $district));
                                    if (blank($get('city'))) {
                                        $set('city', $district);
                                    }
                                    if (blank($get('name'))) {
                                        $set('name', $district);
                                    }
                                    Notification::make()->title("Added {$added} PIN codes from {$district}")->success()->send();
                                }),
                            Action::make('clearAll')
                                ->label('Clear all PIN codes')
                                ->icon('heroicon-o-trash')
                                ->color('gray')
                                ->requiresConfirmation()
                                ->action(fn (Set $set) => $set('pincodes', [])),
                        ])->key('pickerActions')->columnSpanFull(),

                        Select::make('pick_localities')
                            ->label('Or add specific localities')
                            ->helperText('Choose a district above to list all its localities, or search any locality / PIN code.')
                            ->options(fn (Get $get) => PincodeDirectory::localitiesInDistrict($get('pick_state'), $get('pick_district')))
                            ->multiple()
                            ->searchable()
                            ->live()
                            ->dehydrated(false)
                            ->disabled(! $directoryLoaded)
                            ->getSearchResultsUsing(fn (string $search, Get $get) => PincodeDirectory::searchLocalities($search, $get('pick_state'), $get('pick_district')))
                            ->getOptionLabelsUsing(fn (array $values) => array_combine($values, $values))
                            ->afterStateUpdated(function (?array $state, Get $get, Set $set) {
                                if (! $state) {
                                    return;
                                }
                                self::addPincodes($get, $set, array_map(fn ($value) => strtok((string) $value, '|'), $state));
                                $set('pick_localities', []);
                            })
                            ->columnSpanFull(),
                    ]),

                Section::make('PIN codes in this area')
                    ->icon('heroicon-o-map')
                    ->columnSpanFull()
                    ->schema([
                        TagsInput::make('pincodes')
                            ->label('PIN codes')
                            ->placeholder('Add a PIN code manually and press Enter')
                            ->splitKeys(['Tab', ',', ' '])
                            ->required()
                            ->live()
                            ->nestedRecursiveRules(['regex:/^[1-9][0-9]{5}$/'])
                            ->validationMessages(['*.regex' => 'Each PIN code must be 6 digits.'])
                            ->helperText('Bookings are allowed only from these PIN codes. Click × on a PIN code to remove it.'),
                        Placeholder::make('localities_summary')
                            ->label('Localities covered')
                            ->visible($directoryLoaded)
                            ->content(function (Get $get) {
                                $pins = ServiceArea::normalisePincodes($get('pincodes') ?? []);
                                if (! $pins) {
                                    return 'No PIN codes yet.';
                                }
                                $names = PincodeDirectory::localityNames($pins);
                                $lines = collect($pins)->take(40)->map(fn ($pin) => '<strong>' . e($pin) . '</strong> – ' . e($names[$pin] ?? 'not in directory'));
                                $more = count($pins) > 40 ? '<br><em>…and ' . (count($pins) - 40) . ' more</em>' : '';

                                return new HtmlString('<div style="max-height:260px;overflow:auto;font-size:.82rem;line-height:1.7">' . $lines->implode('<br>') . $more . '</div>');
                            }),
                    ]),

                Section::make('Services offered')
                    ->description('Leave empty to offer every service in this area.')
                    ->icon('heroicon-o-squares-2x2')
                    ->columnSpanFull()
                    ->schema([
                        Select::make('categories')
                            ->label('Only these services')
                            ->relationship('categories', 'name')
                            ->multiple()
                            ->preload()
                            ->placeholder('All services'),
                        Textarea::make('notes')->label('Internal notes')->rows(2),
                    ]),
            ]);
    }
}
