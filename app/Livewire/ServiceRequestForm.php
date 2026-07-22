<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ServiceRequest;
use Illuminate\Support\Facades\Auth;

class ServiceRequestForm extends Component
{
    public $event_date;
    public $event_time;
    public $guest_count;
    public $budget_range_min;
    public $budget_range_max;
    public $cuisine_preferences = [];
    public $details;

    // Guest user fields
    public $name = '';
    public $email = '';
    public $phone = '';

    // Direct booking fields
    public $chef_id;
    public $chef_name;

    public function mount()
    {
        $chefId = request()->query('chef_id');
        if ($chefId) {
            $chef = \App\Models\User::role('chef')->find($chefId);
            if ($chef) {
                $this->chef_id = $chef->id;
                $this->chef_name = $chef->name;
            }
        }

        $eventDate = request()->query('event_date');
        if ($eventDate) {
            $this->event_date = $eventDate;
        }

        $guestCount = request()->query('guest_count');
        if ($guestCount) {
            $this->guest_count = (int) $guestCount;
        }

        $location = request()->query('location');
        if ($location) {
            $this->details = "Event Location: " . $location . "\n" . $this->details;
        }
    }

    protected function rules(): array
    {
        $rules = [
            'event_date' => 'required|date|after:today',
            'event_time' => 'required',
            'guest_count' => 'required|integer|min:1',
            'budget_range_min' => 'nullable|numeric|min:0',
            'budget_range_max' => 'nullable|numeric|gt:budget_range_min',
            'cuisine_preferences' => 'nullable|array',
            'details' => 'nullable|string',
        ];

        if (!Auth::check()) {
            $rules['name'] = 'required|string|max:255';
            $rules['email'] = 'required|string|lowercase|email|max:255|unique:\App\Models\User,email';
            $rules['phone'] = 'required|string|regex:/^(?:\+91|0)?[6-9]\d{9}$/';
        }

        return $rules;
    }

    protected function messages(): array
    {
        return [
            'phone.regex' => 'Please enter a valid 10-digit Indian mobile number.',
            'email.unique' => 'This email is already registered. Please sign in first.',
        ];
    }

    public function submit()
    {
        $this->validate();

        // Create user if guest
        if (!Auth::check()) {
            $user = \App\Models\User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(16)),
                'phone' => $this->phone,
            ]);

            $user->assignRole('user');

            Auth::login($user);
        }

        $request = ServiceRequest::create([
            'user_id' => Auth::id(),
            'chef_id' => $this->chef_id,
            'event_date' => $this->event_date,
            'event_time' => $this->event_time,
            'guest_count' => $this->guest_count,
            'budget_range_min' => $this->budget_range_min,
            'budget_range_max' => $this->budget_range_max,
            'cuisine_preferences' => $this->cuisine_preferences,
            'details' => $this->details,
            'status' => 'open',
        ]);

        // Auto-create proposal if a specific chef is selected
        if ($this->chef_id) {
            \App\Models\Proposal::create([
                'service_request_id' => $request->id,
                'chef_id' => $this->chef_id,
                'menu_details' => 'Direct Booking Request: Please accept this request to confirm availability and discuss custom menu details.',
                'price' => $this->budget_range_max ?: ($this->budget_range_min ?: 0.00),
                'status' => 'pending',
            ]);
        }

        session()->flash('message', __('Your request has been submitted successfully!'));

        return redirect()->to('/dashboard');
    }

    public function render()
    {
        return view('livewire.service-request-form');
    }
}
