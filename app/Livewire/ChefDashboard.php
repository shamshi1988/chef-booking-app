<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ChefDashboard extends Component
{
    use WithFileUploads;

    // Tab control
    public string $activeTab = 'bookings';

    // Profile form fields
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public int $experience_years = 0;
    public string $location = '';
    public string $bio = '';
    public array $specialties = [];
    public $avatar;
    public ?string $currentAvatarUrl = null;

    public array $availableSpecialties = [
        'Indian', 'French', 'Italian', 'Mediterranean', 'Japanese', 
        'Mexican', 'Thai', 'Seafood', 'Pastry', 'Vegan', 'Continental'
    ];

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';

        $profile = $user->chefProfile;
        if ($profile) {
            $this->experience_years = $profile->experience_years;
            $this->location = $profile->location ?? '';
            $this->bio = $profile->bio ?? '';
            $this->specialties = $profile->specialties ?? [];
            $this->currentAvatarUrl = $profile->avatar_url;
        }
    }

    public function saveProfile()
    {
        $user = Auth::user();

        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['required', 'string', 'regex:/^(?:\+91|0)?[6-9]\d{9}$/'],
            'experience_years' => ['required', 'integer', 'min:0', 'max:60'],
            'location' => ['required', 'string', 'max:255'],
            'bio' => ['required', 'string', 'min:10', 'max:1000'],
            'specialties' => ['required', 'array', 'min:1'],
            'avatar' => ['nullable', 'image', 'max:2048'], // 2MB Max
        ], [
            'phone.regex' => 'Please enter a valid 10-digit Indian mobile number.',
            'specialties.required' => 'Please select at least one specialty.',
        ]);

        $user->update([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
        ]);

        $profileData = [
            'experience_years' => $this->experience_years,
            'location' => $this->location,
            'bio' => $this->bio,
            'specialties' => $this->specialties,
        ];

        if ($this->avatar) {
            $path = $this->avatar->store('avatars', 'public');
            $profileData['avatar_url'] = $path;
            $this->currentAvatarUrl = $path;
            $this->avatar = null; // Reset temp upload
        }

        $user->chefProfile()->updateOrCreate(
            ['user_id' => $user->id],
            $profileData
        );

        session()->flash('success', 'Profile updated successfully!');
    }

    public function render()
    {
        $openRequests = ServiceRequest::where('status', 'open')
            ->latest()
            ->get();

        $assignedRequests = ServiceRequest::whereHas('proposals', function ($query) {
            $query->where('chef_id', Auth::id())
                  ->where('status', 'accepted');
        })->latest()->get();

        return view('livewire.chef-dashboard', [
            'openRequests' => $openRequests,
            'assignedRequests' => $assignedRequests,
        ]);
    }
}
