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

    protected $rules = [
        'event_date' => 'required|date|after:today',
        'event_time' => 'required',
        'guest_count' => 'required|integer|min:1',
        'budget_range_min' => 'nullable|numeric|min:0',
        'budget_range_max' => 'nullable|numeric|gt:budget_range_min',
        'cuisine_preferences' => 'nullable|array',
        'details' => 'nullable|string',
    ];

    public function submit()
    {
        $this->validate();

        ServiceRequest::create([
            'user_id' => Auth::id(),
            'event_date' => $this->event_date,
            'event_time' => $this->event_time,
            'guest_count' => $this->guest_count,
            'budget_range_min' => $this->budget_range_min,
            'budget_range_max' => $this->budget_range_max,
            'cuisine_preferences' => $this->cuisine_preferences,
            'details' => $this->details,
            'status' => 'open',
        ]);

        session()->flash('message', __('Your request has been submitted successfully! Chefs will contact you soon.'));

        return redirect()->to('/dashboard');
    }

    public function render()
    {
        return view('livewire.service-request-form');
    }
}
