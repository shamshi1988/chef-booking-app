<?php

namespace App\Filament\Resources\ServiceRequests\Schemas;

use App\Filament\Resources\Users\UserResource;
use App\Models\ServiceRequest;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ServiceRequestInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Grid::make(1)->columnSpan(['lg' => 2])->schema([
                    Section::make('Request')
                        ->icon('heroicon-o-clipboard-document-list')
                        ->columns(3)
                        ->schema([
                            TextEntry::make('id')->label('Request #')->prefix('#'),
                            TextEntry::make('category.name')->label('Service')->badge(),
                            TextEntry::make('status')->badge()
                                ->formatStateUsing(fn (string $state) => ServiceRequest::STATUSES[$state] ?? $state)
                                ->color(fn (string $state) => match ($state) { 'open' => 'warning', 'closed' => 'success', 'completed' => 'primary', default => 'danger' }),
                            TextEntry::make('preferred_date')->label('Preferred date')->date('l, d M Y'),
                            TextEntry::make('preferred_time')->label('Time slot')
                                ->formatStateUsing(fn ($state, ServiceRequest $record) => $record->timeSlotLabel()),
                            TextEntry::make('created_at')->label('Received')->dateTime('d M Y, h:i A')
                                ->helperText(fn (ServiceRequest $record) => $record->created_at?->diffForHumans()),
                        ]),

                    Section::make('Service details')
                        ->description('Answers to the questions for this service')
                        ->icon('heroicon-o-list-bullet')
                        ->schema([
                            KeyValueEntry::make('answer_summary')
                                ->hiddenLabel()
                                ->keyLabel('Question')
                                ->valueLabel('Answer')
                                ->state(fn (ServiceRequest $record) => $record->answerSummary())
                                ->placeholder('No extra details'),
                            TextEntry::make('details')->label('Customer note')->placeholder('—')->columnSpanFull(),
                            TextEntry::make('budget')->label('Budget')
                                ->state(fn (ServiceRequest $record) => ($record->budget_range_min || $record->budget_range_max)
                                    ? '₹' . number_format((float) $record->budget_range_min) . ($record->budget_range_max ? ' – ₹' . number_format((float) $record->budget_range_max) : '+')
                                    : null)
                                ->placeholder('Not given'),
                        ]),

                    Section::make('Address')
                        ->icon('heroicon-o-map-pin')
                        ->columns(3)
                        ->schema([
                            TextEntry::make('address')->columnSpanFull()->copyable()->placeholder('—'),
                            TextEntry::make('city')->placeholder('—'),
                            TextEntry::make('pincode')->label('PIN code')->placeholder('—'),
                        ]),
                ]),

                Grid::make(1)->columnSpan(['lg' => 1])->schema([
                    Section::make('Customer')
                        ->icon('heroicon-o-user')
                        ->schema([
                            TextEntry::make('user.name')->label('Name')->weight('bold')
                                ->url(fn (ServiceRequest $record) => $record->user ? UserResource::getUrl('view', ['record' => $record->user]) : null),
                            TextEntry::make('user.phone')->label('Mobile')->copyable()->placeholder('Not given')
                                ->url(fn (ServiceRequest $record) => $record->user?->phone ? 'tel:' . $record->user->phone : null),
                            TextEntry::make('user.email')->label('Email')->copyable()
                                ->url(fn (ServiceRequest $record) => $record->user ? 'mailto:' . $record->user->email : null),
                            TextEntry::make('user.created_at')->label('Customer since')->date('d M Y'),
                        ]),

                    Section::make('Professional')
                        ->icon('heroicon-o-wrench-screwdriver')
                        ->schema([
                            TextEntry::make('assigned')->label('Assigned professional')
                                ->state(fn (ServiceRequest $record) => $record->proposals()->where('status', 'accepted')->with('provider')->first()?->provider?->name)
                                ->placeholder('Not assigned yet')
                                ->weight('bold'),
                            TextEntry::make('assigned_phone')->label('Their mobile')
                                ->state(fn (ServiceRequest $record) => $record->proposals()->where('status', 'accepted')->with('provider')->first()?->provider?->phone)
                                ->copyable()
                                ->placeholder('—'),
                            TextEntry::make('provider.name')->label('Requested by customer')->placeholder('No specific professional requested'),
                            TextEntry::make('completed_on')->label('Completed on')
                                ->state(fn (ServiceRequest $record) => $record->acceptedBooking()?->completed_at?->format('d M Y, h:i A'))
                                ->placeholder('Not completed yet'),
                            TextEntry::make('customer_rating')->label('Customer rating')
                                ->state(function (ServiceRequest $record) {
                                    $rating = $record->acceptedBooking()?->rating;

                                    return $rating ? str_repeat('★', $rating->rating) . str_repeat('☆', 5 - $rating->rating) . ($rating->comment ? ' – “' . $rating->comment . '”' : '') : null;
                                })
                                ->placeholder('Not rated yet'),
                        ]),
                ]),
            ]);
    }
}
