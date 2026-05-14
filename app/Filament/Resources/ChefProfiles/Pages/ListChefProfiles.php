<?php

namespace App\Filament\Resources\ChefProfiles\Pages;

use App\Filament\Resources\ChefProfiles\ChefProfileResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListChefProfiles extends ListRecords
{
    protected static string $resource = ChefProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
