<?php

namespace App\Filament\Resources\ChefProfiles\Pages;

use App\Filament\Resources\ChefProfiles\ChefProfileResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditChefProfile extends EditRecord
{
    protected static string $resource = ChefProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
