<?php

namespace App\Filament\Resources\Users\Tables;

use App\Filament\Resources\Users\UserResource;
use App\Filament\Resources\Users\Schemas\UserInfolist;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(fn ($record) => UserResource::getUrl('view', ['record' => $record]))
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn (Builder $query) => $query->with('roles')->withCount('serviceRequests'))
            ->columns([
                TextColumn::make('name')->searchable()->weight('bold'),
                TextColumn::make('role')->label('Type')->badge()
                    ->state(fn (User $record) => UserInfolist::roleLabel($record))
                    ->color(fn (string $state) => match ($state) { 'Admin' => 'danger', 'Professional' => 'info', default => 'gray' }),
                TextColumn::make('phone')->label('Mobile')->searchable()->copyable()->placeholder('—'),
                TextColumn::make('email')->label('Email')->searchable()->copyable(),
                TextColumn::make('service_requests_count')->label('Requests')->sortable(),
                TextColumn::make('created_at')->label('Registered')->date('d M Y')->sortable()
                    ->description(fn (User $record) => $record->created_at?->diffForHumans()),
                TextColumn::make('email_verified_at')->label('Email verified')->date('d M Y')->placeholder('No')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options(['user' => 'Customers', 'provider' => 'Professionals', 'admin' => 'Admins'])
                    ->query(function (Builder $query, array $data) {
                        if (! $data['value']) {
                            return $query;
                        }
                        if ($data['value'] === 'user') {
                            return $query->whereDoesntHave('roles', fn ($q) => $q->whereIn('name', ['provider', 'admin']));
                        }

                        return $query->whereHas('roles', fn ($q) => $q->where('name', $data['value']));
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
