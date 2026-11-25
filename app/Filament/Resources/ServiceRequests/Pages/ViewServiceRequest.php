<?php

namespace App\Filament\Resources\ServiceRequests\Pages;

use App\Filament\Actions\AssignProviderAction;
use App\Filament\Actions\CompleteBookingAction;
use App\Filament\Resources\ServiceRequests\ServiceRequestResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewServiceRequest extends ViewRecord
{
    protected static string $resource = ServiceRequestResource::class;

    public function getTitle(): string
    {
        return 'Booking request #' . $this->record->id;
    }

    protected function getHeaderActions(): array
    {
        return [
            AssignProviderAction::make()->after(fn () => $this->refreshFormData(['status'])),
            CompleteBookingAction::make()->after(fn () => $this->refreshFormData(['status'])),
            EditAction::make(),
        ];
    }
}
