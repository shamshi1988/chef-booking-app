<?php

namespace App\Livewire;

use App\Models\Proposal;
use App\Models\ServiceArea;
use App\Models\ServiceCategory;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * Booking enquiry form for any service. The service-specific questions come from
 * ServiceCategory::form_fields, so new services need no code changes.
 */
class ServiceRequestForm extends Component
{
    public ?int $service_category_id = null;
    public bool $lockCategory = false;

    /** Answers to the selected service's own questions, keyed by field key. */
    public array $answers = [];

    public $preferred_date;
    public string $preferred_time = '';
    public string $address = '';
    public string $city = '';
    public string $pincode = '';

    /** Live result of the service-area check for the entered PIN code: null | 'ok' | 'no-area' | 'no-service' */
    public ?string $areaStatus = null;
    public ?string $areaName = null;

    /** "Your area / locality" box: picking a suggestion fills the PIN code and city. */
    public string $locality = '';
    public $budget_range_min;
    public $budget_range_max;
    public string $details = '';

    // Guest contact details (an account is created automatically)
    public string $name = '';
    public string $email = '';
    public string $phone = '';

    // Booking a specific professional
    public ?int $provider_id = null;
    public ?string $provider_name = null;

    public function mount(?int $categoryId = null): void
    {
        if ($categoryId) {
            $this->service_category_id = $categoryId;
            $this->lockCategory = true;
        } elseif ($slug = request()->query('service')) {
            $this->service_category_id = ServiceCategory::active()->where('slug', $slug)->value('id');
        }

        if ($providerId = request()->query('provider_id')) {
            $provider = User::providers()->with('providerProfile')->find($providerId);
            if ($provider) {
                $this->provider_id = $provider->id;
                $this->provider_name = $provider->name;
                $this->service_category_id ??= $provider->providerProfile?->service_category_id;
            }
        }

        $this->city = (string) (request()->query('city') ?? request()->query('location') ?? '');
        if ($date = request()->query('date')) {
            $this->preferred_date = $date;
        }

        if (Auth::check()) {
            $this->phone = (string) Auth::user()->phone;
        }

        $this->resetAnswers();
    }

    public function updatedPincode(): void
    {
        $this->checkArea();
    }

    public function updatedLocality(): void
    {
        if (preg_match('/([1-9][0-9]{5})\s*$/', $this->locality, $m)) {
            $this->pincode = $m[1];
            $this->checkArea();
        }
    }

    /** Served localities to suggest to the customer (only when the PIN directory is loaded). */
    #[Computed]
    public function localitySuggestions(): array
    {
        if (! ServiceArea::restrictionEnabled()) {
            return [];
        }

        return \App\Models\PincodeDirectory::suggestionsFor(ServiceArea::bookablePincodes($this->service_category_id));
    }

    /** Check the PIN code against the admin's service areas and fill in the city. */
    public function checkArea(): void
    {
        $this->areaStatus = null;
        $this->areaName = null;

        $pin = preg_replace('/\D/', '', $this->pincode);
        if (strlen($pin) !== 6 || ! ServiceArea::restrictionEnabled()) {
            return;
        }

        if ($area = ServiceArea::findFor($pin, $this->service_category_id)) {
            $this->areaStatus = 'ok';
            $this->areaName = $area->name . ', ' . $area->city;
            $this->city = $area->city;
            $this->resetValidation(['pincode', 'city']);
        } elseif (ServiceArea::pincodeServed($pin)) {
            $this->areaStatus = 'no-service';
        } else {
            $this->areaStatus = 'no-area';
        }
    }

    public function updatedServiceCategoryId(): void
    {
        unset($this->category);
        $this->resetAnswers();
        $this->resetValidation();
        unset($this->localitySuggestions);
        $this->checkArea();
    }

    #[Computed]
    public function category(): ?ServiceCategory
    {
        return $this->service_category_id
            ? ServiceCategory::active()->find($this->service_category_id)
            : null;
    }

    #[Computed]
    public function services()
    {
        return ServiceCategory::active()->get(['id', 'name', 'icon']);
    }

    protected function resetAnswers(): void
    {
        $this->answers = [];
        foreach ($this->category?->fields() ?? [] as $field) {
            $this->answers[$field['key']] = $field['type'] === 'multiselect' ? [] : '';
        }
    }

    protected function rules(): array
    {
        $rules = [
            'service_category_id' => ['required', Rule::exists('service_categories', 'id')->where('is_active', true)],
            'preferred_date' => ['required', 'date', 'after_or_equal:today'],
            'preferred_time' => ['required', Rule::in(array_keys(ServiceRequest::TIME_SLOTS))],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'pincode' => ServiceArea::restrictionEnabled()
                ? ['required', 'regex:/^[1-9][0-9]{5}$/', function ($attribute, $value, $fail) {
                    if (! ServiceArea::findFor($value, $this->service_category_id)) {
                        $fail(ServiceArea::pincodeServed($value)
                            ? __('This service is not available in PIN code :pin yet.', ['pin' => $value])
                            : __('Sorry, we do not serve PIN code :pin yet.', ['pin' => $value]));
                    }
                }]
                : ['nullable', 'regex:/^[1-9][0-9]{5}$/'],
            'budget_range_min' => ['nullable', 'numeric', 'min:0'],
            'budget_range_max' => ['nullable', 'numeric', 'min:0', 'gte:budget_range_min'],
            'details' => ['nullable', 'string', 'max:2000'],
        ];

        foreach ($this->category?->fields() ?? [] as $field) {
            $key = 'answers.' . $field['key'];
            $presence = $field['required'] ? 'required' : 'nullable';

            $rules[$key] = match ($field['type']) {
                'number' => [$presence, 'integer', 'min:0', 'max:100000'],
                'select' => [$presence, Rule::in($field['options'])],
                'multiselect' => [$presence, 'array', $field['required'] ? 'min:1' : 'min:0'],
                'textarea' => [$presence, 'string', 'max:2000'],
                default => [$presence, 'string', 'max:255'],
            };

            if ($field['type'] === 'multiselect') {
                $rules[$key . '.*'] = [Rule::in($field['options'])];
            }
        }

        if (! Auth::check()) {
            $rules['name'] = ['required', 'string', 'max:255'];
            $rules['email'] = ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'];
            $rules['phone'] = ['required', 'string', 'regex:/^(?:\+91|0)?[6-9]\d{9}$/'];
        } else {
            $rules['phone'] = ['nullable', 'string', 'regex:/^(?:\+91|0)?[6-9]\d{9}$/'];
        }

        return $rules;
    }

    protected function messages(): array
    {
        return [
            'service_category_id.required' => __('Please choose a service.'),
            'phone.regex' => __('Please enter a valid 10-digit Indian mobile number.'),
            'pincode.regex' => __('Please enter a valid 6-digit PIN code.'),
            'email.unique' => __('This email is already registered. Please log in first.'),
            'preferred_date.after_or_equal' => __('Please choose today or a future date.'),
        ];
    }

    protected function validationAttributes(): array
    {
        $attributes = [
            'preferred_date' => __('date'),
            'preferred_time' => __('time slot'),
            'budget_range_max' => __('maximum budget'),
            'budget_range_min' => __('minimum budget'),
        ];
        foreach ($this->category?->fields() ?? [] as $field) {
            $attributes['answers.' . $field['key']] = mb_strtolower(__($field['label']));
        }

        return $attributes;
    }

    public function submit()
    {
        $this->validate();

        // Keep the city consistent with the service area the PIN code belongs to.
        if ($area = ServiceArea::findFor($this->pincode, $this->service_category_id)) {
            $this->city = $area->city;
        }

        if (! Auth::check()) {
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make(Str::random(16)),
                'phone' => $this->phone,
            ]);
            $user->assignRole('user');
            Auth::login($user);
        } elseif ($this->phone && ! Auth::user()->phone) {
            Auth::user()->update(['phone' => $this->phone]);
        }

        // Keep only answers that belong to this service and were filled in.
        $fieldKeys = collect($this->category->fields())->pluck('key')->all();
        $answers = collect($this->answers)
            ->only($fieldKeys)
            ->reject(fn ($value) => $value === '' || $value === null || $value === [])
            ->map(fn ($value) => is_numeric($value) && ! is_array($value) ? $value + 0 : $value)
            ->all();

        $request = ServiceRequest::create([
            'user_id' => Auth::id(),
            'service_category_id' => $this->service_category_id,
            'provider_id' => $this->provider_id,
            'preferred_date' => $this->preferred_date,
            'preferred_time' => $this->preferred_time,
            'address' => $this->address,
            'city' => $this->city,
            'pincode' => $this->pincode ?: null,
            'budget_range_min' => $this->budget_range_min ?: null,
            'budget_range_max' => $this->budget_range_max ?: null,
            'details' => $this->details ?: null,
            'answers' => $answers,
            'status' => 'open',
        ]);

        // Booking a specific professional creates a pending request for them to confirm.
        if ($this->provider_id) {
            Proposal::create([
                'service_request_id' => $request->id,
                'provider_id' => $this->provider_id,
                'message' => 'Direct booking request from customer.',
                'price' => $this->budget_range_max ?: ($this->budget_range_min ?: 0),
                'status' => 'pending',
            ]);
        }

        session()->flash('success', __('Your :service request has been received! We will call you shortly to confirm.', [
            'service' => __($this->category->name),
        ]));

        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.service-request-form', [
            'timeSlots' => ServiceRequest::TIME_SLOTS,
        ]);
    }
}
