<?php

namespace App\Http\Controllers;

use App\Models\ServiceCategory;
use App\Models\User;

class ServiceController extends Controller
{
    public function home()
    {
        return view('welcome', [
            'services' => ServiceCategory::active()->get(),
            'providerCount' => User::providers()->count(),
        ]);
    }

    public function index()
    {
        return view('services.index', [
            'services' => ServiceCategory::active()->get(),
        ]);
    }

    public function show(ServiceCategory $service)
    {
        abort_unless($service->is_active, 404);

        return view('services.show', [
            'service' => $service,
            'otherServices' => ServiceCategory::active()->whereKeyNot($service->id)->take(4)->get(),
        ]);
    }

    public function book()
    {
        return view('book');
    }
}
