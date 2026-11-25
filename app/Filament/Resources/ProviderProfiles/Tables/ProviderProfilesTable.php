<?php

namespace App\Filament\Resources\ProviderProfiles\Tables;

use App\Filament\Resources\ProviderProfiles\ProviderProfileResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ProviderProfilesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(fn ($record) => ProviderProfileResource::getUrl('view', ['record' => $record]))
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('user.name')->label('Name')->searchable()->weight('bold'),
                TextColumn::make('user.phone')->label('Mobile')->searchable()->copyable(),
                TextColumn::make('user.email')->label('Email')->searchable()->toggleable(),
                TextColumn::make('category.name')->label('Service')->badge(),
                TextColumn::make('location')->searchable(),
                TextColumn::make('experience_years')->label('Exp. (yrs)')->sortable(),
                ToggleColumn::make('is_verified')->label('Verified'),
                TextColumn::make('created_at')->label('Joined')->since()->sortable(),
            ])
            ->filters([
                SelectFilter::make('service_category_id')->label('Service')->relationship('category', 'name'),
                TernaryFilter::make('is_verified')->label('Verified'),
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
