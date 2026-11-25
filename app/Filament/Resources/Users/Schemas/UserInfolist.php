<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Filament\Resources\ProviderProfiles\ProviderProfileResource;
use App\Filament\Resources\ServiceRequests\ServiceRequestResource;
use App\Models\ServiceRequest;
use App\Models\User;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function roleLabel(User $user): string
    {
        $roles = $user->roles->pluck('name')->unique();

        return match (true) {
            $roles->contains('admin') => 'Admin',
            $roles->contains('provider') => 'Professional',
            default => 'Customer',
        };
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Grid::make(1)->columnSpan(['lg' => 2])->schema([
                    Section::make('Registration details')
                        ->icon('heroicon-o-identification')
                        ->columns(2)
                        ->schema([
                            TextEntry::make('name')->label('Full name')->weight('bold'),
                            TextEntry::make('role')->label('Account type')->badge()
                                ->state(fn (User $record) => self::roleLabel($record))
                                ->color(fn (string $state) => match ($state) { 'Admin' => 'danger', 'Professional' => 'info', default => 'gray' }),
                            TextEntry::make('phone')->label('Mobile number')->copyable()->placeholder('Not given')
                                ->url(fn (User $record) => $record->phone ? 'tel:' . $record->phone : null),
                            TextEntry::make('email')->label('Email')->copyable()
                                ->url(fn (User $record) => 'mailto:' . $record->email),
                            TextEntry::make('created_at')->label('Registered on')->dateTime('d M Y, h:i A'),
                            IconEntry::make('email_verified')->label('Email verified')->boolean()
                                ->state(fn (User $record) => (bool) $record->email_verified_at),
                        ]),

                    Section::make('Booking requests')
                        ->description('Services this customer has booked')
                        ->icon('heroicon-o-clipboard-document-list')
                        ->schema([
                            RepeatableEntry::make('serviceRequests')
                                ->hiddenLabel()
                                ->state(fn (User $record) => ServiceRequest::with('category')->where('user_id', $record->id)->latest()->take(10)->get())
                                ->columns(4)
                                ->placeholder('No booking requests yet')
                                ->schema([
                                    TextEntry::make('category.name')->label('Service')->badge()
                                        ->url(fn (ServiceRequest $record) => ServiceRequestResource::getUrl('view', ['record' => $record])),
                                    TextEntry::make('preferred_date')->label('Date')->date('d M Y'),
                                    TextEntry::make('city')->placeholder('—'),
                                    TextEntry::make('status')->badge()
                                        ->formatStateUsing(fn (string $state) => ServiceRequest::STATUSES[$state] ?? $state)
                                        ->color(fn (string $state) => match ($state) { 'open' => 'warning', 'closed' => 'success', default => 'danger' }),
                                ]),
                        ]),
                ]),

                Grid::make(1)->columnSpan(['lg' => 1])->schema([
                    Section::make('Summary')
                        ->icon('heroicon-o-chart-bar')
                        ->schema([
                            TextEntry::make('requests_count')->label('Total booking requests')
                                ->state(fn (User $record) => ServiceRequest::where('user_id', $record->id)->count()),
                            TextEntry::make('open_count')->label('Pending requests')
                                ->state(fn (User $record) => ServiceRequest::where('user_id', $record->id)->where('status', 'open')->count()),
                            TextEntry::make('professional_profile')->label('Professional profile')
                                ->state(fn (User $record) => $record->providerProfile ? 'View profile →' : null)
                                ->url(fn (User $record) => $record->providerProfile ? ProviderProfileResource::getUrl('view', ['record' => $record->providerProfile]) : null)
                                ->placeholder('Not a professional')
                                ->color('primary'),
                        ]),
                ]),
            ]);
    }
}
