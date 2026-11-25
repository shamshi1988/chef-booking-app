<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    protected function messages(): array
    {
        return ['phone.regex' => __('Please enter a valid 10-digit Indian mobile number.')];
    }

    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['nullable', 'string', 'regex:/^(?:\+91|0)?[6-9]\d{9}$/'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['phone'] = $validated['phone'] ?: null;

        event(new Registered($user = User::create($validated)));

        if (\Spatie\Permission\Models\Role::where('name', 'user')->where('guard_name', 'web')->exists()) {
            $user->assignRole('user');
        }

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <h2>{{ __('Create an account') }}</h2>
    <p class="auth-sub">{{ __('Create a free account to book trusted home services.') }}</p>

    <form wire:submit="register" class="form" novalidate>
        <div @class(['field', 'has-error' => $errors->has('name')])>
            <label class="label" for="name">{{ __('Full name') }}</label>
            <div class="input-icon">
                <x-ui-icon name="user" />
                <input wire:model="name" id="name" type="text" class="input" required autofocus autocomplete="name">
            </div>
            @error('name') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="form-row form-row-2">
            <div @class(['field', 'has-error' => $errors->has('email')])>
                <label class="label" for="email">{{ __('Email') }}</label>
                <div class="input-icon">
                    <x-ui-icon name="mail" />
                    <input wire:model="email" id="email" type="email" class="input" required autocomplete="username">
                </div>
                @error('email') <span class="error">{{ $message }}</span> @enderror
            </div>
            <div @class(['field', 'has-error' => $errors->has('phone')])>
                <label class="label" for="phone">{{ __('Mobile number') }}</label>
                <div class="input-icon">
                    <x-ui-icon name="phone" />
                    <input wire:model="phone" id="phone" type="tel" class="input" autocomplete="tel" placeholder="9876543210">
                </div>
                @error('phone') <span class="error">{{ $message }}</span> @enderror
            </div>
        </div>

        <div @class(['field', 'has-error' => $errors->has('password')])>
            <label class="label" for="password">{{ __('Password') }}</label>
            <div class="input-icon">
                <x-ui-icon name="shield" />
                <input wire:model="password" id="password" type="password" class="input has-toggle" required autocomplete="new-password">
                <button type="button" class="pass-toggle" data-toggle-password="password" data-show="{{ __('Show') }}" data-hide="{{ __('Hide') }}" aria-pressed="false">{{ __('Show') }}</button>
            </div>
            @error('password') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div @class(['field', 'has-error' => $errors->has('password_confirmation')])>
            <label class="label" for="password_confirmation">{{ __('Confirm password') }}</label>
            <div class="input-icon">
                <x-ui-icon name="shield" />
                <input wire:model="password_confirmation" id="password_confirmation" type="password" class="input" required autocomplete="new-password">
            </div>
            @error('password_confirmation') <span class="error">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="btn btn-primary btn-lg btn-block" wire:loading.attr="disabled" wire:target="register">
            <span wire:loading wire:target="register" class="spinner"></span>
            {{ __('Create account') }}
        </button>

        <div class="divider">{{ __('Already registered?') }}</div>
        <a href="{{ route('login') }}" class="btn btn-outline btn-block">{{ __('Log in') }}</a>
        <p class="hint text-center">
            {{ __('Are you a professional?') }} <a class="link" href="{{ route('become-partner') }}">{{ __('Join as a Partner') }}</a>
        </p>
    </form>
</div>
