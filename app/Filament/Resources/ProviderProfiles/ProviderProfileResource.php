<?php

namespace App\Filament\Resources\ProviderProfiles;

use App\Filament\Resources\ProviderProfiles\Pages\CreateProviderProfile;
use App\Filament\Resources\ProviderProfiles\Pages\EditProviderProfile;
use App\Filament\Resources\ProviderProfiles\Pages\ListProviderProfiles;
use App\Filament\Resources\ProviderProfiles\Pages\ViewProviderProfile;
use App\Filament\Resources\ProviderProfiles\Schemas\ProviderProfileInfolist;
use App\Filament\Resources\ProviderProfiles\Schemas\ProviderProfileForm;
use App\Filament\Resources\ProviderProfiles\Tables\ProviderProfilesTable;
use App\Models\ProviderProfile;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProviderProfileResource extends Resource
{
    protected static ?string $model = ProviderProfile::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    protected static ?string $modelLabel = 'professional';

    protected static ?string $pluralModelLabel = 'professionals';

    protected static string|\UnitEnum|null $navigationGroup = 'People';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Professionals';

    public static function getNavigationBadge(): ?string
    {
        $pending = \App\Models\ProviderProfile::where('is_verified', false)->count();

        return $pending ? (string) $pending : null;
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Waiting for verification';
    }

    public static function form(Schema $schema): Schema
    {
        return ProviderProfileForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProviderProfileInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProviderProfilesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProviderProfiles::route('/'),
            'create' => CreateProviderProfile::route('/create'),
            'view' => ViewProviderProfile::route('/{record}'),
            'edit' => EditProviderProfile::route('/{record}/edit'),
        ];
    }
}
