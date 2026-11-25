<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ServiceRequests\ServiceRequestResource;
use App\Models\ServiceRequest;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestRequests extends TableWidget
{
    protected static ?int $sort = 4;

    protected static ?string $heading = 'Latest booking requests';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(ServiceRequest::query()->with(['category', 'user'])->latest())
            ->defaultPaginationPageOption(5)
            ->paginated([5])
            ->columns([
                TextColumn::make('id')->label('#'),
                TextColumn::make('category.name')->label('Service')->badge(),
                TextColumn::make('user.name')->label('Customer')
                    ->description(fn (ServiceRequest $record) => $record->user?->phone),
                TextColumn::make('preferred_date')->label('Date')->date('d M Y')
                    ->description(fn (ServiceRequest $record) => $record->timeSlotLabel()),
                TextColumn::make('city')->placeholder('—'),
                TextColumn::make('status')->badge()
                    ->formatStateUsing(fn (string $state) => ServiceRequest::STATUSES[$state] ?? $state)
                    ->color(fn (string $state) => match ($state) {
                        'open' => 'warning',
                        'closed' => 'success',
                        default => 'danger',
                    }),
                TextColumn::make('created_at')->label('Received')->since(),
            ])
            ->recordActions([
                Action::make('open')
                    ->label('Open')
                    ->icon('heroicon-m-arrow-right')
                    ->url(fn (ServiceRequest $record) => ServiceRequestResource::getUrl('edit', ['record' => $record])),
            ])
            ->headerActions([
                Action::make('all')
                    ->label('View all')
                    ->link()
                    ->url(ServiceRequestResource::getUrl('index')),
            ])
            ->emptyStateHeading('No booking requests yet')
            ->emptyStateDescription('New requests from the website will appear here.');
    }
}
