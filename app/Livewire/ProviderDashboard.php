<?php

namespace App\Livewire;

use App\Models\Booking;
use App\Models\Proposal;
use App\Models\ServiceCategory;
use App\Models\ServiceRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProviderDashboard extends Component
{
    use WithFileUploads;

    public string $activeTab = 'jobs';

    // Profile form
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public ?int $service_category_id = null;
    public int $experience_years = 0;
    public string $location = '';
    public string $bio = '';
    public string $skills = '';
    public $avatar;
    public ?string $currentAvatarUrl = null;

    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';

        if ($profile = $user->providerProfile) {
            $this->service_category_id = $profile->service_category_id;
            $this->experience_years = $profile->experience_years;
            $this->location = $profile->location ?? '';
            $this->bio = $profile->bio ?? '';
            $this->skills = implode(', ', $profile->skills ?? []);
            $this->currentAvatarUrl = $profile->avatar_url;
        }

        if (session()->has('tab')) {
            $this->activeTab = session('tab');
        }
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = in_array($tab, ['jobs', 'requests', 'account']) ? $tab : 'jobs';
    }

    /** Accept a customer's direct booking request. */
    public function acceptRequest(int $proposalId): void
    {
        $proposal = $this->pendingDirectProposal($proposalId);
        if (! $proposal) {
            session()->flash('error', __('This request is no longer available.'));
            return;
        }

        DB::transaction(function () use ($proposal) {
            $proposal->update(['status' => 'accepted']);
            Booking::create(['proposal_id' => $proposal->id, 'status' => 'confirmed', 'payment_status' => 'pending']);
            $proposal->serviceRequest->update(['status' => 'closed', 'provider_id' => Auth::id()]);
        });

        session()->flash('success', __('Booking accepted. The customer details are now in My Jobs.'));
    }

    /** The assigned professional marks their job as done; the customer is then asked for a rating. */
    public function completeJob(int $requestId): void
    {
        $booking = Booking::where('status', 'confirmed')
            ->whereHas('proposal', fn ($q) => $q->where('provider_id', Auth::id())->where('status', 'accepted')->where('service_request_id', $requestId))
            ->first();

        if (! $booking) {
            session()->flash('error', __('This request is no longer available.'));

            return;
        }

        $booking->markCompleted();
        session()->flash('success', __('Job marked as completed. The customer will be asked to rate your service.'));
    }

    public function declineRequest(int $proposalId): void
    {
        $proposal = $this->pendingDirectProposal($proposalId);
        if ($proposal) {
            $proposal->update(['status' => 'rejected']);
            // Let the team assign someone else.
            $proposal->serviceRequest->update(['provider_id' => null]);
            session()->flash('success', __('Request declined. Our team will assign another professional.'));
        }
    }

    private function pendingDirectProposal(int $proposalId): ?Proposal
    {
        return Proposal::where('id', $proposalId)
            ->where('provider_id', Auth::id())
            ->where('status', 'pending')
            ->whereHas('serviceRequest', fn ($q) => $q->where('status', 'open'))
            ->first();
    }

    public function saveProfile(): void
    {
        $user = Auth::user();

        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['required', 'string', 'regex:/^(?:\+91|0)?[6-9]\d{9}$/'],
            'service_category_id' => ['required', Rule::exists('service_categories', 'id')],
            'experience_years' => ['required', 'integer', 'min:0', 'max:60'],
            'location' => ['required', 'string', 'max:255'],
            'bio' => ['required', 'string', 'min:10', 'max:1000'],
            'skills' => ['nullable', 'string', 'max:500'],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ], [
            'phone.regex' => __('Please enter a valid 10-digit Indian mobile number.'),
        ]);

        $user->update([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
        ]);

        $profileData = [
            'service_category_id' => $this->service_category_id,
            'experience_years' => $this->experience_years,
            'location' => $this->location,
            'bio' => $this->bio,
            'skills' => ProviderRegistrationForm::parseSkills($this->skills),
        ];

        if ($this->avatar) {
            $path = $this->avatar->store('avatars', 'public');
            $profileData['avatar_url'] = $path;
            $this->currentAvatarUrl = $path;
            $this->avatar = null;
        }

        $user->providerProfile()->updateOrCreate(['user_id' => $user->id], $profileData);

        session()->flash('success', __('Profile updated successfully!'));
    }

    public function render()
    {
        $user = Auth::user();
        $categoryId = $user->providerProfile?->service_category_id;

        $assignedRequests = ServiceRequest::with(['category', 'user'])
            ->whereHas('proposals', fn ($q) => $q->where('provider_id', $user->id)->where('status', 'accepted'))
            ->orderByRaw("status = 'completed'")
            ->orderBy('preferred_date')
            ->get()
            ->each(fn (ServiceRequest $request) => $request->setRelation('booking', $request->acceptedBooking()));

        $directRequests = Proposal::with(['serviceRequest.category'])
            ->where('provider_id', $user->id)
            ->where('status', 'pending')
            ->whereHas('serviceRequest', fn ($q) => $q->where('status', 'open'))
            ->latest()
            ->get();

        // Open jobs in the professional's own service (without customer contact details).
        $openRequests = ServiceRequest::with('category')
            ->where('status', 'open')
            ->whereNull('provider_id')
            ->when($categoryId, fn ($q) => $q->where('service_category_id', $categoryId))
            ->latest()
            ->get();

        return view('livewire.provider-dashboard', [
            'assignedRequests' => $assignedRequests,
            'directRequests' => $directRequests,
            'openRequests' => $openRequests,
            'categories' => ServiceCategory::active()->get(['id', 'name']),
            'profile' => $user->providerProfile,
        ]);
    }
}
