<?php

namespace App\Filament\Resources\ServiceAreas\Pages;

use App\Filament\Resources\ServiceAreas\ServiceAreaResource;
use App\Models\ServiceArea;
use App\Models\ServiceCategory;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListServiceAreas extends ListRecords
{
    protected static string $resource = ServiceAreaResource::class;

    public function getSubheading(): ?string
    {
        return ServiceArea::restrictionEnabled()
            ? 'Bookings are accepted only from the PIN codes in active areas below.'
            : 'No active areas yet – bookings are currently accepted from any PIN code. Add an area to start restricting.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('checkPin')
                ->label('Check a PIN code')
                ->icon('heroicon-o-magnifying-glass')
                ->color('gray')
                ->modalWidth('md')
                ->modalSubmitActionLabel('Check')
                ->schema([
                    TextInput::make('pincode')->label('PIN code')->required()->regex('/^[1-9][0-9]{5}$/')
                        ->validationMessages(['regex' => 'Enter a valid 6-digit PIN code.']),
                    Select::make('service_category_id')->label('Service (optional)')
                        ->options(ServiceCategory::active()->pluck('name', 'id')),
                ])
                ->action(function (array $data) {
                    if (! ServiceArea::restrictionEnabled()) {
                        Notification::make()->title('No restriction active')->body('Bookings are accepted from every PIN code until you add an active area.')->info()->send();

                        return;
                    }
                    $area = ServiceArea::findFor($data['pincode'], $data['service_category_id'] ?? null);
                    if ($area) {
                        Notification::make()->title('Bookings allowed')->body("{$data['pincode']} is in {$area->name}, {$area->city}.")->success()->send();
                    } elseif (ServiceArea::pincodeServed($data['pincode'])) {
                        Notification::make()->title('Service not offered here')->body("{$data['pincode']} is in a service area, but that service is not offered there.")->warning()->send();
                    } else {
                        Notification::make()->title('Outside service area')->body("Bookings from {$data['pincode']} are not accepted.")->danger()->send();
                    }
                }),
            CreateAction::make(),
        ];
    }
}
