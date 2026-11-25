<?php

namespace App\Filament\Widgets;

use App\Models\ServiceCategory;
use Filament\Widgets\ChartWidget;

class RequestsByServiceChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected ?string $heading = 'Requests by service';

    protected ?string $description = 'All time';

    protected ?string $maxHeight = '260px';

    protected int | string | array $columnSpan = 1;

    private const PALETTE = ['#315CFF', '#6C63FF', '#18B7A0', '#F4B740', '#E85D75', '#20B486', '#0EA5E9', '#8E9BAE', '#EC4899', '#84CC16'];

    protected function getData(): array
    {
        $services = ServiceCategory::withCount('serviceRequests')
            ->having('service_requests_count', '>', 0)
            ->orderByDesc('service_requests_count')
            ->get();

        return [
            'datasets' => [[
                'data' => $services->pluck('service_requests_count')->all(),
                'backgroundColor' => array_slice(self::PALETTE, 0, max(1, $services->count())),
                'borderWidth' => 0,
            ]],
            'labels' => $services->pluck('name')->all(),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'cutout' => '68%',
            'plugins' => ['legend' => ['position' => 'bottom', 'labels' => ['usePointStyle' => true, 'boxWidth' => 8]]],
            'scales' => ['x' => ['display' => false], 'y' => ['display' => false]],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
