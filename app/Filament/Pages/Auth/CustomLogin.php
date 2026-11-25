<?php

namespace App\Filament\Pages\Auth;

use App\Models\ServiceCategory;
use Filament\Auth\Pages\Login as BaseLogin;
use Illuminate\Support\Collection;

class CustomLogin extends BaseLogin
{
    protected string $view = 'filament.pages.auth.login';

    public function getHeading(): string
    {
        return config('app.name');
    }

    /** Active services shown on the brand side of the login page. */
    public function getServiceList(): Collection
    {
        return ServiceCategory::active()->take(8)->get(['name', 'icon']);
    }
}
