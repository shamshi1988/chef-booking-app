<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public bool $saved = false;

    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
        $this->phone = (string) Auth::user()->phone;
    }

    public function updated(): void
    {
        $this->saved = false;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'phone' => ['nullable', 'string', 'regex:/^(?:\+91|0)?[6-9]\d{9}$/'],
        ], [
            'phone.regex' => __('Please enter a valid 10-digit Indian mobile number.'),
        ]);
        $validated['phone'] = $validated['phone'] ?: null;

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->saved = true;
        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<form wire:submit="updateProfileInformation" class="s-card" novalidate>
    <div class="s-head">
        <span class="icon-badge sm"><x-ui-icon name="user" /></span>
        <div>
            <h3>{{ __('Personal details') }}</h3>
            <p>{{ __('Your name and contact details used for bookings.') }}</p>
        </div>
    </div>

    <div class="s-body">
        <div @class(['field', 'has-error' => $errors->has('name')])>
            <label class="label" for="profile_name">{{ __('Full name') }}</label>
            <div class="input-icon">
                <x-ui-icon name="user" />
                <input wire:model="name" id="profile_name" type="text" class="input" required autocomplete="name">
            </div>
            @error('name') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="form-row form-row-2">
            <div @class(['field', 'has-error' => $errors->has('email')])>
                <label class="label" for="profile_email">{{ __('Email') }}</label>
                <div class="input-icon">
                    <x-ui-icon name="mail" />
                    <input wire:model="email" id="profile_email" type="email" class="input" required autocomplete="username">
                </div>
                @error('email') <span class="error">{{ $message }}</span> @enderror
            </div>
            <div @class(['field', 'has-error' => $errors->has('phone')])>
                <label class="label" for="profile_phone">{{ __('Mobile number') }}</label>
                <div class="input-icon">
                    <x-ui-icon name="phone" />
                    <input wire:model="phone" id="profile_phone" type="tel" class="input" autocomplete="tel" placeholder="9876543210">
                </div>
                @error('phone') <span class="error">{{ $message }}</span> @enderror
                <span class="hint">{{ __('We call this number to confirm your bookings.') }}</span>
            </div>
        </div>

        @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
            <div class="alert alert-info">
                <span>
                    {{ __('Your email address is unverified.') }}
                    <button type="button" wire:click.prevent="sendVerification" class="link" style="background:none;border:0;padding:0;cursor:pointer;font:inherit;">{{ __('Click here to re-send the verification email.') }}</button>
                    @if (session('status') === 'verification-link-sent')
                        <br><strong>{{ __('A new verification link has been sent to your email address.') }}</strong>
                    @endif
                </span>
            </div>
        @endif
    </div>

    <div class="s-foot">
        @if ($saved)
            <span class="saved"><x-ui-icon name="check" /> {{ __('Saved.') }}</span>
        @endif
        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="updateProfileInformation">
            <span wire:loading wire:target="updateProfileInformation" class="spinner"></span>
            {{ __('Save changes') }}
        </button>
    </div>
</form>
