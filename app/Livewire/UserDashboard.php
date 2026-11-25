<?php

namespace App\Livewire;

use App\Models\Booking;
use App\Models\Rating;
use App\Models\ServiceRequest;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UserDashboard extends Component
{
    // Rating pop-up
    public ?int $ratingBookingId = null;
    public int $ratingValue = 0;
    public string $commentText = '';

    public function cancelRequest(int $id): void
    {
        $request = ServiceRequest::where('user_id', Auth::id())->where('status', 'open')->find($id);

        if ($request) {
            $request->update(['status' => 'cancelled']);
            $request->proposals()->where('status', 'pending')->update(['status' => 'rejected']);
            session()->flash('success', __('Your booking request has been cancelled.'));
        }
    }

    /** Only completed bookings of this customer's own requests, with the assigned professional, not yet rated. */
    private function rateableBooking(?int $bookingId): ?Booking
    {
        return $bookingId
            ? Booking::awaitingRatingBy(Auth::id())->with('proposal.provider')->find($bookingId)
            : null;
    }

    public function openRating(int $bookingId): void
    {
        if (! $this->rateableBooking($bookingId)) {
            session()->flash('error', __('You can only rate professionals who completed a booking for you.'));

            return;
        }

        $this->resetValidation();
        $this->ratingBookingId = $bookingId;
        $this->ratingValue = 0;
        $this->commentText = '';
    }

    public function closeRating(): void
    {
        $this->ratingBookingId = null;
    }

    public function submitRating(): void
    {
        $booking = $this->rateableBooking($this->ratingBookingId);
        if (! $booking) {
            $this->closeRating();
            session()->flash('error', __('You are not allowed to rate this professional.'));

            return;
        }

        $this->validate([
            'ratingValue' => ['required', 'integer', 'min:1', 'max:5'],
            'commentText' => ['nullable', 'string', 'max:1000'],
        ], [
            'ratingValue.min' => __('Please choose 1 to 5 stars.'),
        ]);

        Rating::create([
            'user_id' => Auth::id(),
            'provider_id' => $booking->proposal->provider_id,
            'booking_id' => $booking->id,
            'rating' => $this->ratingValue,
            'comment' => $this->commentText !== '' ? $this->commentText : null,
        ]);

        $this->closeRating();
        session()->flash('success', __('Thank you for your rating!'));
    }

    public function render()
    {
        $requests = ServiceRequest::with(['category'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get()
            ->each(fn (ServiceRequest $request) => $request->setRelation('booking', $request->acceptedBooking()));

        $awaiting = Booking::awaitingRatingBy(Auth::id())->with('proposal.serviceRequest.category', 'proposal.provider')->get();

        return view('livewire.user-dashboard', [
            'requests' => $requests,
            'awaitingRating' => $awaiting,
            'ratingBooking' => $this->ratingBookingId ? $this->rateableBooking($this->ratingBookingId) : null,
        ]);
    }
}
