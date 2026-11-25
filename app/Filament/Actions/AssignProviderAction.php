<?php

namespace App\Filament\Actions;

use App\Models\Booking;
use App\Models\Proposal;
use App\Models\ServiceRequest;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\DB;

/** "Assign professional" for an open booking request (used in the list and on the detail page). */
class AssignProviderAction
{
    public static function make(): Action
    {
        return Action::make('assignProvider')
            ->label('Assign professional')
            ->icon('heroicon-o-user-plus')
            ->color('success')
            ->schema(fn (ServiceRequest $record) => [
                Select::make('provider_id')
                    ->label('Professional')
                    ->helperText('Professionals for this service are listed first.')
                    ->options(function () use ($record) {
                        return User::providers()->with('providerProfile.category')->orderBy('name')->get()
                            ->sortByDesc(fn ($u) => $u->providerProfile?->service_category_id === $record->service_category_id)
                            ->mapWithKeys(fn ($u) => [$u->id => $u->name . ' – ' . ($u->providerProfile?->category?->name ?? 'no service set')]);
                    })
                    ->default($record->provider_id)
                    ->searchable()
                    ->required(),
                TextInput::make('price')->label('Agreed price')->numeric()->prefix('₹')
                    ->default($record->budget_range_max ?? $record->category?->starting_price ?? 0),
            ])
            ->action(function (ServiceRequest $record, array $data): void {
                DB::transaction(function () use ($record, $data) {
                    // Any other pending offers for this request are no longer needed.
                    $record->proposals()->where('status', 'pending')->where('provider_id', '!=', $data['provider_id'])->update(['status' => 'rejected']);

                    $proposal = Proposal::updateOrCreate(
                        ['service_request_id' => $record->id, 'provider_id' => $data['provider_id']],
                        ['message' => 'Assigned by administrator', 'price' => $data['price'] ?? 0, 'status' => 'accepted']
                    );
                    Booking::firstOrCreate(['proposal_id' => $proposal->id], ['status' => 'confirmed', 'payment_status' => 'pending']);

                    $record->update(['status' => 'closed', 'provider_id' => $data['provider_id']]);
                });
            })
            ->successNotificationTitle('Professional assigned')
            ->visible(fn (ServiceRequest $record) => $record->status === 'open');
    }
}
