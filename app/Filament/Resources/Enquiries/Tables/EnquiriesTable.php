<?php

namespace App\Filament\Resources\Enquiries\Tables;

use App\Models\Enquiry;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EnquiriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')->searchable()->weight('bold'),
                TextColumn::make('phone')->searchable()->copyable(),
                TextColumn::make('category.name')->label('Service')->placeholder('General'),
                TextColumn::make('city')->toggleable(),
                TextColumn::make('message')->limit(60)->tooltip(fn (Enquiry $record) => $record->message),
                TextColumn::make('status')->badge()
                    ->formatStateUsing(fn (string $state) => Enquiry::STATUSES[$state] ?? $state)
                    ->color(fn (string $state) => match ($state) {
                        'new' => 'warning',
                        'contacted' => 'info',
                        default => 'success',
                    }),
                TextColumn::make('created_at')->label('Received')->since()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options(Enquiry::STATUSES),
                SelectFilter::make('service_category_id')->label('Service')->relationship('category', 'name'),
            ])
            ->recordActions([
                Action::make('contacted')
                    ->label('Mark contacted')
                    ->icon('heroicon-o-phone')
                    ->color('info')
                    ->visible(fn (Enquiry $record) => $record->status === 'new')
                    ->action(fn (Enquiry $record) => $record->update(['status' => 'contacted'])),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
