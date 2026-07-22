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
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="relative">
    <!-- Close Button -->
    <a href="{{ url('/') }}" class="absolute -top-4 -right-4 w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 text-gray-500 hover:bg-gray-200 transition focus:outline-none">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
    </a>

    <div class="text-center mb-8">
        <h2 class="text-[32px] font-light text-brand-primary mb-3">{{ __('Create an account') }}</h2>
        <p class="text-[15px] text-slate-800">{{ __('Your culinary adventure begins here.') }}</p>
    </div>

    <form wire:submit="register" class="flex flex-col gap-2">
        <!-- Name -->
        <div>
            <input wire:model="name" id="name" type="text" name="name" required autofocus autocomplete="name"
                class="block w-full px-6 py-3.5 border-gray-200 focus:border-brand-primary focus:ring-0 rounded-full shadow-sm placeholder-gray-400 text-[15px]"
                placeholder="{{ __('Name') }}">
            <x-input-error :messages="$errors->get('name')" class="mt-2 text-center" />
        </div>

        <!-- Email Address -->
        <div>
            <input wire:model="email" id="email" type="email" name="email" required autocomplete="username"
                class="block w-full px-6 py-3.5 border-gray-200 focus:border-brand-primary focus:ring-0 rounded-full shadow-sm placeholder-gray-400 text-[15px]"
                placeholder="{{ __('Email Address') }}">
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-center" />
        </div>

        <!-- Password -->
        <div>
            <input wire:model="password" id="password" type="password" name="password" required autocomplete="new-password"
                class="block w-full px-6 py-3.5 border-gray-200 focus:border-brand-primary focus:ring-0 rounded-full shadow-sm placeholder-gray-400 text-[15px]"
                placeholder="{{ __('Password') }}">
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-center" />
        </div>

        <!-- Confirm Password -->
        <div>
            <input wire:model="password_confirmation" id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                class="block w-full px-6 py-3.5 border-gray-200 focus:border-brand-primary focus:ring-0 rounded-full shadow-sm placeholder-gray-400 text-[15px]"
                placeholder="{{ __('Confirm Password') }}">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-center" />
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full bg-brand-primary text-white font-bold text-[15px] py-3.5 rounded-full hover:bg-brand-primary-dark transition shadow-sm">
                {{ __('Register') }}
            </button>
        </div>

        <div class="text-center pt-2">
            <a class="text-[15px] font-bold text-slate-900 underline hover:text-slate-600 transition" href="{{ route('login') }}" wire:navigate>
                {{ __('Already registered?') }}
            </a>
        </div>
    </form>

    <div class="mt-8">
        <div class="relative flex items-center">
            <div class="flex-grow border-t border-gray-200"></div>
            <span class="flex-shrink-0 mx-4 text-gray-400 text-sm">{{ __('Or continue with') }}</span>
            <div class="flex-grow border-t border-gray-200"></div>
        </div>

        <div class="mt-6 flex justify-center gap-6">
            <a href="#" class="w-[72px] h-[72px] bg-white border border-gray-100 rounded-2xl shadow-sm flex items-center justify-center hover:shadow-md transition">
                <svg class="w-6 h-6" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                </svg>
            </a>
            <a href="#" class="w-[72px] h-[72px] bg-white border border-gray-100 rounded-2xl shadow-sm flex items-center justify-center hover:shadow-md transition">
                <svg class="w-7 h-7 text-black" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path d="M17.05 20.28c-.98.02-1.95-.31-2.75-.91-.79-.6-1.57-1.12-2.45-1.12-.87 0-1.63.5-2.4 1.12-.76.61-1.74.96-2.73.96-3.23 0-6.15-4.43-6.15-8.52 0-3.32 1.69-5.96 4.75-5.96 1.43 0 2.5.83 3.51.83 1.07 0 2.45-.98 3.97-.98 1.6 0 2.82.52 3.73 1.64-3.13 1.77-2.58 6.13.56 7.42-.64 2.19-2.02 4.09-3.44 5.52zM12.03 5.4c-.16-2.59 2.02-4.59 4.39-4.71.3 2.76-2.3 4.88-4.39 4.71z"/>
                </svg>
            </a>
        </div>
    </div>
</div>
