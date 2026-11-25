<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Contracts\Support\Htmlable;

class Dashboard extends BaseDashboard
{
    public function getHeading(): string | Htmlable
    {
        return 'Dashboard';
    }

    public function getColumns(): int | array
    {
        return ['md' => 2, 'xl' => 3];
    }
}
