<?php

namespace App\Filament\Resources\ChefProfiles;

use App\Filament\Resources\ChefProfiles\Pages\CreateChefProfile;
use App\Filament\Resources\ChefProfiles\Pages\EditChefProfile;
use App\Filament\Resources\ChefProfiles\Pages\ListChefProfiles;
use App\Filament\Resources\ChefProfiles\Schemas\ChefProfileForm;
use App\Filament\Resources\ChefProfiles\Tables\ChefProfilesTable;
use App\Models\ChefProfile;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ChefProfileResource extends Resource
{
    protected static ?string $model = ChefProfile::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ChefProfileForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ChefProfilesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListChefProfiles::route('/'),
            'create' => CreateChefProfile::route('/create'),
            'edit' => EditChefProfile::route('/{record}/edit'),
        ];
    }
}
