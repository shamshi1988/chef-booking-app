<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ServiceRequest;
use Illuminate\Support\Facades\Auth;

class UserDashboard extends Component
{
    public function render()
    {
        $requests = ServiceRequest::where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('livewire.user-dashboard', [
            'requests' => $requests
        ]);
    }
}
