<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ServiceCategories\ServiceCategoryResource;
use App\Filament\Resources\ServiceRequests\ServiceRequestResource;
use App\Models\ServiceRequest;
use Filament\Widgets\Widget;

/** Navy welcome banner at the top of the dashboard. */
class WelcomeBanner extends Widget
{
    protected static ?int $sort = 0;

    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = 'full';

    protected string $view = 'filament.widgets.welcome-banner';

    protected function getViewData(): array
    {
        $hour = (int) now()->format('G');

        return [
            'greeting' => $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening'),
            'name' => strtok((string) auth()->user()?->name, ' ') ?: 'Admin',
            'openCount' => ServiceRequest::where('status', 'open')->count(),
            'requestsUrl' => ServiceRequestResource::getUrl('index', ['tableFilters' => ['status' => ['value' => 'open']]]),
            'servicesUrl' => ServiceCategoryResource::getUrl('create'),
        ];
    }
}
