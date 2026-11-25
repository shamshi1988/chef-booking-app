<?php

namespace App\Filament\Resources\ProviderProfiles\Pages;

use App\Filament\Resources\ProviderProfiles\ProviderProfileResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewProviderProfile extends ViewRecord
{
    protected static string $resource = ProviderProfileResource::class;

    public function getTitle(): string
    {
        return $this->record->user?->name ?? 'Professional';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('verify')
                ->label(fn () => $this->record->is_verified ? 'Remove verification' : 'Mark as verified')
                ->icon(fn () => $this->record->is_verified ? 'heroicon-o-x-circle' : 'heroicon-o-check-badge')
                ->color(fn () => $this->record->is_verified ? 'gray' : 'success')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->update(['is_verified' => ! $this->record->is_verified]);
                    $this->refreshFormData(['is_verified']);
                }),
            EditAction::make(),
        ];
    }
}
