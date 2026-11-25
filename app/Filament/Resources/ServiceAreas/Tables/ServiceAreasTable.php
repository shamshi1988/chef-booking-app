<?php

namespace App\Filament\Resources\ServiceAreas\Tables;

use App\Models\ServiceArea;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ServiceAreasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('city')
            ->modifyQueryUsing(fn ($query) => $query->with('categories'))
            ->columns([
                TextColumn::make('name')->label('Area')->searchable()->weight('bold')
                    ->description(fn (ServiceArea $record) => $record->notes),
                TextColumn::make('city')->searchable()->sortable()->badge()->color('gray'),
                TextColumn::make('pincodes')->label('PIN codes')
                    ->state(fn (ServiceArea $record) => count($record->pincodes ?? []) . ' PIN codes')
                    ->description(fn (ServiceArea $record) => \Illuminate\Support\Str::limit(implode(', ', $record->pincodes ?? []), 60))
                    ->searchable(query: fn ($query, string $search) => $query->where('pincodes', 'like', '%' . preg_replace('/\D/', '', $search) . '%')),
                TextColumn::make('services')->label('Services')
                    ->state(fn (ServiceArea $record) => $record->categories->isEmpty() ? 'All services' : $record->categories->pluck('name')->implode(', '))
                    ->wrap(),
                ToggleColumn::make('is_active')->label('Active'),
                TextColumn::make('updated_at')->label('Updated')->since()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('city')->options(fn () => ServiceArea::query()->distinct()->orderBy('city')->pluck('city', 'city')->all()),
                TernaryFilter::make('is_active')->label('Active'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No service areas yet')
            ->emptyStateDescription('Bookings are accepted from every PIN code until you add an active area.');
    }
}
