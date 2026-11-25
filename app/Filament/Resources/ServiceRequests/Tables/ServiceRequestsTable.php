<?php

namespace App\Filament\Resources\ServiceRequests\Tables;

use App\Filament\Resources\ServiceRequests\ServiceRequestResource;
use App\Filament\Actions\AssignProviderAction;
use App\Filament\Actions\CompleteBookingAction;
use App\Models\ServiceRequest;
use Filament\Actions\ViewAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ServiceRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(fn ($record) => ServiceRequestResource::getUrl('view', ['record' => $record]))
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id')->label('#')->sortable(),
                TextColumn::make('category.name')->label('Service')->badge()->sortable(),
                TextColumn::make('user.name')->label('Customer')->searchable()
                    ->description(fn (ServiceRequest $record) => $record->user?->phone),
                TextColumn::make('preferred_date')->label('Date')->date('d M Y')->sortable()
                    ->description(fn (ServiceRequest $record) => $record->timeSlotLabel()),
                TextColumn::make('city')->searchable()->toggleable()
                    ->description(fn (ServiceRequest $record) => $record->pincode),
                TextColumn::make('provider.name')->label('Requested pro')->placeholder('—')->toggleable(),
                TextColumn::make('status')->badge()
                    ->formatStateUsing(fn (string $state) => ServiceRequest::STATUSES[$state] ?? $state)
                    ->color(fn (string $state) => match ($state) {
                        'open' => 'warning',
                        'closed' => 'success',
                        'completed' => 'primary',
                        default => 'danger',
                    }),
                TextColumn::make('created_at')->label('Received')->since()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options(ServiceRequest::STATUSES),
                SelectFilter::make('service_category_id')->label('Service')->relationship('category', 'name'),
            ])
            ->recordActions([
                ViewAction::make(),
                AssignProviderAction::make(),
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
