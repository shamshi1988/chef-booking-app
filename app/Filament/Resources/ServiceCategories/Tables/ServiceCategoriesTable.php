<?php

namespace App\Filament\Resources\ServiceCategories\Tables;

use App\Models\ServiceCategory;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class ServiceCategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                TextColumn::make('name')->searchable()->weight('bold')
                    ->description(fn (ServiceCategory $record) => $record->tagline),
                TextColumn::make('starting_price')->label('From')->money('INR')->placeholder('Quote'),
                TextColumn::make('service_requests_count')->counts('serviceRequests')->label('Requests')->sortable(),
                TextColumn::make('provider_profiles_count')->counts('providerProfiles')->label('Professionals'),
                ToggleColumn::make('is_active')->label('Live'),
                ToggleColumn::make('is_featured')->label('Featured')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (ServiceCategory $record) => route('services.show', $record))
                    ->openUrlInNewTab(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
