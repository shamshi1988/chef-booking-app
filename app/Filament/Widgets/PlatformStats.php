<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Enquiries\EnquiryResource;
use App\Filament\Resources\ProviderProfiles\ProviderProfileResource;
use App\Filament\Resources\ServiceRequests\ServiceRequestResource;
use App\Models\Booking;
use App\Models\Enquiry;
use App\Models\ProviderProfile;
use App\Models\ServiceRequest;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class PlatformStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    protected function getColumns(): int | array
    {
        return ['md' => 2, 'xl' => 4];
    }

    /** Daily counts for the last 7 days, used for the small trend lines. */
    private function trend(Builder $query): array
    {
        $dates = $query->where('created_at', '>=', now()->subDays(6)->startOfDay())->pluck('created_at')
            ->map(fn ($d) => $d->toDateString())->countBy();

        return collect(range(6, 0))->map(fn ($i) => $dates[now()->subDays($i)->toDateString()] ?? 0)->all();
    }

    protected function getStats(): array
    {
        $today = ServiceRequest::whereDate('created_at', today())->count();
        $unverified = ProviderProfile::where('is_verified', false)->count();

        return [
            Stat::make('Open booking requests', ServiceRequest::where('status', 'open')->count())
                ->icon(Heroicon::OutlinedClipboardDocumentList)
                ->description($today . ' new today')
                ->descriptionIcon(Heroicon::ArrowTrendingUp)
                ->chart($this->trend(ServiceRequest::query()))
                ->color('primary')
                ->url(ServiceRequestResource::getUrl('index')),
            Stat::make('Confirmed this month', Booking::where('status', 'confirmed')->where('created_at', '>=', now()->startOfMonth())->count())
                ->icon(Heroicon::OutlinedCalendarDays)
                ->description('Bookings with a professional assigned')
                ->chart($this->trend(Booking::query()))
                ->color('success')
                ->extraAttributes(['class' => 'sh-stat-success']),
            Stat::make('New enquiries', Enquiry::where('status', 'new')->count())
                ->icon(Heroicon::OutlinedChatBubbleLeftRight)
                ->description('Waiting for a call back')
                ->chart($this->trend(Enquiry::query()))
                ->color('warning')
                ->extraAttributes(['class' => 'sh-stat-warning'])
                ->url(EnquiryResource::getUrl('index')),
            Stat::make('Professionals', ProviderProfile::count())
                ->icon(Heroicon::OutlinedWrenchScrewdriver)
                ->description($unverified ? $unverified . ' waiting for verification' : 'All verified')
                ->chart($this->trend(ProviderProfile::query()))
                ->color('info')
                ->extraAttributes(['class' => 'sh-stat-accent'])
                ->url(ProviderProfileResource::getUrl('index')),
        ];
    }
}
