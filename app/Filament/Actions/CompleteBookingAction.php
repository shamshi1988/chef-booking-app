<?php

namespace App\Filament\Actions;

use App\Models\Booking;
use App\Models\ServiceRequest;
use Filament\Actions\Action;

/** Marks the assigned professional's booking as completed, so the customer is asked for a rating. */
class CompleteBookingAction
{
    private static function bookingFor(Booking|ServiceRequest $record): ?Booking
    {
        return $record instanceof Booking ? $record : $record->acceptedBooking();
    }

    public static function make(): Action
    {
        return Action::make('completeBooking')
            ->label('Mark completed')
            ->icon('heroicon-o-check-badge')
            ->color('success')
            ->requiresConfirmation()
            ->modalDescription('The customer will then be asked to rate the professional.')
            ->visible(fn (Booking|ServiceRequest $record) => self::bookingFor($record)?->status === 'confirmed')
            ->action(fn (Booking|ServiceRequest $record) => self::bookingFor($record)?->markCompleted())
            ->successNotificationTitle('Marked as completed');
    }
}
