<?php

namespace App\Filament\Resources\ProviderProfiles\Schemas;

use App\Filament\Resources\Users\UserResource;
use App\Models\Proposal;
use App\Models\ProviderProfile;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProviderProfileInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Grid::make(1)->columnSpan(['lg' => 2])->schema([
                    Section::make('Registration details')
                        ->description('Account information the professional registered with')
                        ->icon('heroicon-o-identification')
                        ->columns(2)
                        ->schema([
                            TextEntry::make('user.name')->label('Full name')->weight('bold')
                                ->url(fn (ProviderProfile $record) => $record->user ? UserResource::getUrl('view', ['record' => $record->user]) : null),
                            TextEntry::make('user.phone')->label('Mobile number')->copyable()->placeholder('Not given')
                                ->url(fn (ProviderProfile $record) => $record->user?->phone ? 'tel:' . $record->user->phone : null),
                            TextEntry::make('user.email')->label('Email')->copyable()
                                ->url(fn (ProviderProfile $record) => $record->user ? 'mailto:' . $record->user->email : null),
                            TextEntry::make('user.created_at')->label('Registered on')->dateTime('d M Y, h:i A'),
                            IconEntry::make('email_verified')->label('Email verified')->boolean()
                                ->state(fn (ProviderProfile $record) => (bool) $record->user?->email_verified_at),
                        ]),

                    Section::make('Professional profile')
                        ->icon('heroicon-o-wrench-screwdriver')
                        ->columns(2)
                        ->schema([
                            TextEntry::make('category.name')->label('Service')->badge()->placeholder('Not set'),
                            TextEntry::make('experience_years')->label('Experience')->suffix(' years'),
                            TextEntry::make('location')->label('City / area served')->placeholder('—'),
                            TextEntry::make('skills')->label('Skills')->badge()->color('gray')->placeholder('—'),
                            TextEntry::make('bio')->label('Introduction')->placeholder('—')->columnSpanFull(),
                        ]),
                ]),

                Grid::make(1)->columnSpan(['lg' => 1])->schema([
                    Section::make('Status')
                        ->icon('heroicon-o-shield-check')
                        ->schema([
                            ImageEntry::make('avatar_url')->label('Photo')->disk('public')->circular()->imageSize(96)
                                ->placeholder('No photo uploaded'),
                            IconEntry::make('is_verified')->label('Verified')->boolean(),
                            TextEntry::make('jobs')->label('Jobs completed / assigned')
                                ->state(fn (ProviderProfile $record) => Proposal::where('provider_id', $record->user_id)->where('status', 'accepted')->count()),
                            TextEntry::make('rating')->label('Customer rating')
                                ->state(function (ProviderProfile $record) {
                                    $count = $record->user?->ratingsReceived()->count();

                                    return $count ? '★ ' . $record->user->averageRating() . ' (' . $count . ' ' . str('review')->plural($count) . ')' : null;
                                })
                                ->placeholder('No ratings yet'),
                            TextEntry::make('updated_at')->label('Profile last updated')->since(),
                        ]),
                ]),
            ]);
    }
}
