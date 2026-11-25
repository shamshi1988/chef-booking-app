<?php

namespace App\Livewire;

use App\Models\ServiceCategory;
use App\Models\User;
use Livewire\Attributes\Url;
use Livewire\Component;

class Professionals extends Component
{
    /** Service slug filter (?service=plumber). */
    #[Url]
    public string $service = '';

    // Ratings are given from "My Bookings" after a completed service, not from this page.

    public function render()
    {
        $categories = ServiceCategory::active()->get(['id', 'name', 'slug', 'icon']);
        $current = $categories->firstWhere('slug', $this->service);

        $providers = User::providers()
            ->with(['providerProfile.category'])
            ->withAvg('ratingsReceived', 'rating')
            ->withCount('ratingsReceived')
            ->when($current, fn ($q) => $q->whereHas('providerProfile', fn ($p) => $p->where('service_category_id', $current->id)))
            ->orderBy('name')
            ->get();

        return view('livewire.professionals', [
            'providers' => $providers,
            'categories' => $categories,
            'current' => $current,
        ])->layout('components.frontend-layout', ['title' => __('Our Professionals')]);
    }
}
