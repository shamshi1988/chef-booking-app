<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <h2>{{ __('Welcome back') }}</h2>
    <p class="auth-sub">{{ __('Log in to book services and track your bookings.') }}</p>

    @if (session('status'))
        <div class="alert alert-success status">{{ session('status') }}</div>
    @endif

    <form wire:submit="login" class="form" novalidate>
        <div @class(['field', 'has-error' => $errors->has('form.email')])>
            <label class="label" for="email">{{ __('Email') }}</label>
            <div class="input-icon">
                <x-ui-icon name="mail" />
                <input wire:model="form.email" id="email" type="email" name="email" class="input" required autofocus autocomplete="username" placeholder="you@example.com">
            </div>
            @error('form.email') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div @class(['field', 'has-error' => $errors->has('form.password')])>
            <div class="form-inline">
                <label class="label" for="password">{{ __('Password') }}</label>
                @if (Route::has('password.request'))
                    <a class="link small" href="{{ route('password.request') }}">{{ __('Forgot your password?') }}</a>
                @endif
            </div>
            <div class="input-icon">
                <x-ui-icon name="shield" />
                <input wire:model="form.password" id="password" type="password" name="password" class="input has-toggle" required autocomplete="current-password">
                <button type="button" class="pass-toggle" data-toggle-password="password" data-show="{{ __('Show') }}" data-hide="{{ __('Hide') }}" aria-pressed="false">{{ __('Show') }}</button>
            </div>
            @error('form.password') <span class="error">{{ $message }}</span> @enderror
        </div>

        <label class="check">
            <input wire:model="form.remember" type="checkbox" name="remember">
            {{ __('Keep me logged in') }}
        </label>

        <button type="submit" class="btn btn-primary btn-lg btn-block" wire:loading.attr="disabled" wire:target="login">
            <span wire:loading wire:target="login" class="spinner"></span>
            {{ __('Log in') }}
        </button>

        <div class="divider">{{ __('New here?') }}</div>

        <a href="{{ route('register') }}" class="btn btn-outline btn-block">{{ __('Create a free account') }}</a>
        <p class="hint text-center">
            {{ __('Are you a professional?') }} <a class="link" href="{{ route('become-partner') }}">{{ __('Join as a Partner') }}</a>
        </p>
    </form>
</div>
