<?php

namespace App\Livewire;

use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

/** Sign-up form for professionals (electricians, plumbers, chefs, physiotherapists, ...). */
class ProviderRegistrationForm extends Component
{
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $password = '';
    public string $password_confirmation = '';
    public ?int $service_category_id = null;
    public int $experience_years = 0;
    public string $location = '';
    public string $skills = '';
    public string $bio = '';

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'phone' => ['required', 'string', 'regex:/^(?:\+91|0)?[6-9]\d{9}$/'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
            'service_category_id' => ['required', Rule::exists('service_categories', 'id')->where('is_active', true)],
            'experience_years' => ['required', 'integer', 'min:0', 'max:60'],
            'location' => ['required', 'string', 'max:255'],
            'skills' => ['nullable', 'string', 'max:500'],
            'bio' => ['required', 'string', 'min:10', 'max:1000'],
        ];
    }

    protected function messages(): array
    {
        return [
            'phone.regex' => __('Please enter a valid 10-digit Indian mobile number (e.g. 9876543210).'),
            'service_category_id.required' => __('Please choose the service you provide.'),
        ];
    }

    public function register()
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'phone' => $this->phone,
        ]);
        $user->assignRole('provider');

        $user->providerProfile()->create([
            'service_category_id' => $this->service_category_id,
            'experience_years' => $this->experience_years,
            'location' => $this->location,
            'skills' => self::parseSkills($this->skills),
            'bio' => $this->bio,
            'is_verified' => false,
        ]);

        Auth::login($user);
        session()->flash('success', __('Welcome aboard! Our team will verify your profile shortly.'));

        return redirect()->route('dashboard');
    }

    /** "Wiring, MCB repair , fans" -> ["Wiring", "MCB repair", "fans"] */
    public static function parseSkills(string $skills): array
    {
        return collect(explode(',', $skills))
            ->map(fn ($skill) => trim($skill))
            ->filter()
            ->unique()
            ->take(15)
            ->values()
            ->all();
    }

    public function render()
    {
        return view('livewire.provider-registration-form', [
            'categories' => ServiceCategory::active()->get(['id', 'name', 'icon']),
        ]);
    }
}
