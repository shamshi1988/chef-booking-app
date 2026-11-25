<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component
{
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';
    public bool $saved = false;

    public function updated(): void
    {
        $this->saved = false;
    }

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->saved = true;
        $this->dispatch('password-updated');
    }
}; ?>

<form wire:submit="updatePassword" class="s-card" novalidate>
    <div class="s-head">
        <span class="icon-badge sm"><x-ui-icon name="shield" /></span>
        <div>
            <h3>{{ __('Password') }}</h3>
            <p>{{ __('Ensure your account is using a long, random password to stay secure.') }}</p>
        </div>
    </div>

    <div class="s-body">
        <div @class(['field', 'has-error' => $errors->has('current_password')]) style="max-width:420px">
            <label class="label" for="update_password_current_password">{{ __('Current Password') }}</label>
            <div class="input-icon">
                <x-ui-icon name="shield" />
                <input wire:model="current_password" id="update_password_current_password" type="password" class="input has-toggle" autocomplete="current-password">
                <button type="button" class="pass-toggle" data-toggle-password="update_password_current_password" data-show="{{ __('Show') }}" data-hide="{{ __('Hide') }}">{{ __('Show') }}</button>
            </div>
            @error('current_password') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="form-row form-row-2">
            <div @class(['field', 'has-error' => $errors->has('password')])>
                <label class="label" for="update_password_password">{{ __('New Password') }}</label>
                <div class="input-icon">
                    <x-ui-icon name="shield" />
                    <input wire:model="password" id="update_password_password" type="password" class="input has-toggle" autocomplete="new-password">
                    <button type="button" class="pass-toggle" data-toggle-password="update_password_password" data-show="{{ __('Show') }}" data-hide="{{ __('Hide') }}">{{ __('Show') }}</button>
                </div>
                @error('password') <span class="error">{{ $message }}</span> @enderror
                <span class="hint">{{ __('At least 8 characters.') }}</span>
            </div>
            <div @class(['field', 'has-error' => $errors->has('password_confirmation')])>
                <label class="label" for="update_password_password_confirmation">{{ __('Confirm Password') }}</label>
                <div class="input-icon">
                    <x-ui-icon name="shield" />
                    <input wire:model="password_confirmation" id="update_password_password_confirmation" type="password" class="input" autocomplete="new-password">
                </div>
                @error('password_confirmation') <span class="error">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>

    <div class="s-foot">
        @if ($saved)
            <span class="saved"><x-ui-icon name="check" /> {{ __('Password updated.') }}</span>
        @endif
        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="updatePassword">
            <span wire:loading wire:target="updatePassword" class="spinner"></span>
            {{ __('Update Password') }}
        </button>
    </div>
</form>
