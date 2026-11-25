<?php

namespace App\Livewire;

use App\Models\Enquiry;
use App\Models\ServiceCategory;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;
use Livewire\Component;

/** General enquiry / call-back form (Contact page). Saved for the team in Admin > Enquiries. */
class EnquiryForm extends Component
{
    public string $name = '';
    public string $phone = '';
    public string $email = '';
    public string $service_category_id = '';
    public string $city = '';
    public string $message = '';
    public bool $sent = false;

    public function mount(): void
    {
        if ($slug = request()->query('service')) {
            $this->service_category_id = (string) ServiceCategory::where('slug', $slug)->value('id');
        }
    }

    public function submit(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^(?:\+91|0)?[6-9]\d{9}$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'service_category_id' => ['nullable', Rule::exists('service_categories', 'id')],
            'city' => ['nullable', 'string', 'max:100'],
            'message' => ['required', 'string', 'min:5', 'max:2000'],
        ], [
            'phone.regex' => __('Please enter a valid 10-digit Indian mobile number.'),
        ]);

        // Basic spam protection: max 5 enquiries per hour from one IP.
        $key = 'enquiry:' . request()->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $this->addError('message', __('Too many enquiries. Please call us instead.'));
            return;
        }
        RateLimiter::hit($key, 3600);

        Enquiry::create([
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email ?: null,
            'service_category_id' => $this->service_category_id ?: null,
            'city' => $this->city ?: null,
            'message' => $this->message,
            'status' => 'new',
        ]);

        $this->reset(['name', 'phone', 'email', 'service_category_id', 'city', 'message']);
        $this->sent = true;
    }

    public function render()
    {
        return view('livewire.enquiry-form', [
            'categories' => ServiceCategory::active()->get(['id', 'name']),
        ]);
    }
}
