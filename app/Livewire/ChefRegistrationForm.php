<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ChefRegistrationForm extends Component
{
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $password = '';
    public string $password_confirmation = '';
    public int $experience_years = 0;
    public string $location = '';
    public string $bio = '';
    public array $specialties = [];
    public bool $isPage = false;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['required', 'string', 'regex:/^(?:\+91|0)?[6-9]\d{9}$/'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
            'experience_years' => ['required', 'integer', 'min:0', 'max:60'],
            'location' => ['required', 'string', 'max:255'],
            'bio' => ['required', 'string', 'min:10', 'max:1000'],
            'specialties' => ['required', 'array', 'min:1'],
        ];
    }

    protected function messages(): array
    {
        return [
            'phone.regex' => 'Please enter a valid 10-digit Indian mobile number (e.g. 9876543210 or +91 9876543210).',
        ];
    }

    public function registerChef()
    {
        $this->validate();

        // 1. Create user
        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'phone' => $this->phone,
        ]);

        // 2. Assign chef role
        $user->assignRole('chef');

        // 3. Create chef profile
        $user->chefProfile()->create([
            'experience_years' => $this->experience_years,
            'location' => $this->location,
            'bio' => $this->bio,
            'specialties' => $this->specialties,
        ]);

        // 4. Log in the user
        Auth::login($user);

        // 5. Redirect to dashboard
        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.chef-registration-form');
    }
}
