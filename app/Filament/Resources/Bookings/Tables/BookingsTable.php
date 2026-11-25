<?php

namespace App\Filament\Resources\Bookings\Tables;

use App\Filament\Actions\CompleteBookingAction;
use App\Filament\Resources\ServiceRequests\ServiceRequestResource;
use App\Models\Booking;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BookingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn ($query) => $query->with(['proposal.provider', 'proposal.serviceRequest.category', 'proposal.serviceRequest.user', 'rating']))
            ->columns([
                TextColumn::make('id')->label('#')->sortable(),
                TextColumn::make('proposal.serviceRequest.category.name')->label('Service')->badge()
                    ->url(fn (Booking $record) => $record->proposal?->serviceRequest ? ServiceRequestResource::getUrl('view', ['record' => $record->proposal->serviceRequest]) : null),
                TextColumn::make('proposal.serviceRequest.user.name')->label('Customer'),
                TextColumn::make('proposal.provider.name')->label('Professional'),
                TextColumn::make('proposal.price')->label('Price')->money('INR'),
                TextColumn::make('status')->badge()
                    ->formatStateUsing(fn (string $state) => Booking::STATUSES[$state] ?? $state)
                    ->color(fn (string $state) => match ($state) { 'confirmed' => 'success', 'completed' => 'primary', default => 'danger' }),
                TextColumn::make('rating.rating')->label('Rating')
                    ->formatStateUsing(fn ($state) => str_repeat('★', (int) $state))
                    ->placeholder('—'),
                TextColumn::make('payment_status')->label('Payment')->badge()->color(fn (string $state) => $state === 'paid' ? 'success' : 'warning'),
                TextColumn::make('completed_at')->label('Completed')->date('d M Y')->placeholder('—')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options(Booking::STATUSES),
            ])
            ->recordActions([
                CompleteBookingAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
