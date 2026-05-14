<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ServiceRequest;

class ChefDashboard extends Component
{
    public function render()
    {
        $openRequests = ServiceRequest::where('status', 'open')
            ->latest()
            ->get();

        return view('livewire.chef-dashboard', [
            'openRequests' => $openRequests
        ]);
    }
}
