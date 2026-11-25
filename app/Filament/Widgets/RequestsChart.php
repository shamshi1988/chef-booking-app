<?php

namespace App\Filament\Widgets;

use App\Models\ServiceRequest;
use Filament\Widgets\ChartWidget;

class RequestsChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Booking requests';

    protected ?string $description = 'Requests received in the last 14 days';

    protected ?string $maxHeight = '260px';

    protected int | string | array $columnSpan = ['md' => 2, 'xl' => 2];

    protected function getData(): array
    {
        $counts = ServiceRequest::where('created_at', '>=', now()->subDays(13)->startOfDay())
            ->pluck('created_at')->map(fn ($d) => $d->toDateString())->countBy();

        $days = collect(range(13, 0))->map(fn ($i) => now()->subDays($i));

        return [
            'datasets' => [[
                'label' => 'Requests',
                'data' => $days->map(fn ($d) => $counts[$d->toDateString()] ?? 0)->all(),
                'fill' => true,
                'tension' => 0.35,
                'borderColor' => '#315CFF',
                'backgroundColor' => 'rgba(49, 92, 255, 0.10)',
                'pointBackgroundColor' => '#315CFF',
                'pointRadius' => 3,
            ]],
            'labels' => $days->map(fn ($d) => $d->format('d M'))->all(),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => ['legend' => ['display' => false]],
            'scales' => [
                'y' => ['beginAtZero' => true, 'ticks' => ['precision' => 0], 'grid' => ['color' => 'rgba(148,163,184,.15)']],
                'x' => ['grid' => ['display' => false]],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
