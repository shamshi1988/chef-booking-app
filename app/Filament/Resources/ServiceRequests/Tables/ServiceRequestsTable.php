<?php

namespace App\Filament\Resources\ServiceRequests\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ServiceRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('event_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('event_time')
                    ->time()
                    ->sortable(),
                TextColumn::make('guest_count')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('budget_range_min')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('budget_range_max')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\Action::make('assignChef')
                    ->label('Assign Chef')
                    ->icon('heroicon-o-user')
                    ->color('success')
                    ->form([
                        \Filament\Forms\Components\Select::make('chef_id')
                            ->label('Select Chef')
                            ->options(fn () => \App\Models\User::role('chef', 'web')->pluck('name', 'id'))
                            ->required(),
                    ])
                    ->action(function (\App\Models\ServiceRequest $record, array $data): void {
                        $proposal = \App\Models\Proposal::create([
                            'service_request_id' => $record->id,
                            'chef_id' => $data['chef_id'],
                            'menu_details' => 'Assigned directly by Administrator',
                            'price' => $record->budget_range_max ?? 0,
                            'status' => 'accepted',
                        ]);

                        \App\Models\Booking::create([
                            'proposal_id' => $proposal->id,
                            'status' => 'confirmed',
                            'payment_status' => 'pending',
                        ]);

                        $record->update(['status' => 'closed']);
                    })
                    ->visible(fn (\App\Models\ServiceRequest $record) => $record->status === 'open'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
