<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Booking;
use App\Models\Rating;
use Illuminate\Support\Facades\Auth;

class OurChefs extends Component
{
    public int $ratingValue = 5;
    public string $commentText = '';
    public ?int $selectedChefId = null;
    public bool $showRatingModal = false;

    public function openRatingModal(int $chefId)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $chef = User::findOrFail($chefId);

        if (!$this->canRateChef($chef)) {
            session()->flash('error', 'You can only rate chefs assigned to your bookings.');
            return;
        }

        $this->selectedChefId = $chefId;
        $this->ratingValue = 5;
        $this->commentText = '';
        $this->showRatingModal = true;
    }

    public function submitRating()
    {
        if (!Auth::check() || !$this->selectedChefId) {
            return;
        }

        $chef = User::findOrFail($this->selectedChefId);

        if (!$this->canRateChef($chef)) {
            session()->flash('error', 'You are not authorized to rate this chef.');
            return;
        }

        $booking = Booking::whereHas('proposal', function ($query) {
            $query->where('chef_id', $this->selectedChefId)
                  ->whereHas('serviceRequest', function ($subQuery) {
                      $subQuery->where('user_id', Auth::id());
                  });
        })->whereDoesntHave('rating')->first();

        if (!$booking) {
            session()->flash('error', 'No unrated booking found for this chef.');
            return;
        }

        Rating::create([
            'user_id' => Auth::id(),
            'chef_id' => $this->selectedChefId,
            'booking_id' => $booking->id,
            'rating' => $this->ratingValue,
            'comment' => $this->commentText,
        ]);

        $this->showRatingModal = false;
        $this->selectedChefId = null;
        $this->commentText = '';
        session()->flash('success', 'Thank you for your rating!');
    }

    public function canRateChef(User $chef): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return Booking::whereHas('proposal', function ($query) use ($chef) {
            $query->where('chef_id', $chef->id)
                  ->whereHas('serviceRequest', function ($subQuery) {
                      $subQuery->where('user_id', Auth::id());
                  });
        })->whereDoesntHave('rating')->exists();
    }

    public function render()
    {
        $chefs = User::role('chef', 'web')
            ->with(['chefProfile', 'ratingsReceived'])
            ->get();

        return view('livewire.our-chefs', [
            'chefs' => $chefs
        ])->layout('components.frontend-layout');
    }
}
