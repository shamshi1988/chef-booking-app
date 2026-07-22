<div class="bg-white p-8 md:p-12 rounded-2xl shadow-sm border border-gray-100">
    <h2 class="text-2xl font-serif font-bold mb-8 text-slate-900">{{ __('Submit Your Event Requirements') }}</h2>

    @if($chef_name)
        <div class="mb-8 bg-brand-primary/5 border border-brand-primary/20 text-brand-primary rounded-2xl p-4 flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            <span class="text-sm font-semibold">{{ __('You are requesting a booking with Chef:') }} <strong class="underline">{{ $chef_name }}</strong></span>
        </div>
    @endif

    <form wire:submit.prevent="submit" class="space-y-6">
        @if(!Auth::check())
            <div class="space-y-4 mb-8 pb-8 border-b border-gray-100 text-left">
                <h3 class="text-lg font-semibold text-slate-900">{{ __('Your Contact Details') }}</h3>
                <p class="text-sm text-slate-500 mb-4">{{ __('We will automatically create an account for you so you can track your request, view proposals, and chat with chefs.') }}</p>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">{{ __('Full Name') }}</label>
                        <input type="text" wire:model="name" class="w-full rounded-lg border-gray-300 focus:border-brand-primary focus:ring-brand-primary shadow-sm py-3 text-slate-900" placeholder="{{ __('John Doe') }}">
                        @error('name') <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">{{ __('Email Address') }}</label>
                        <input type="email" wire:model="email" class="w-full rounded-lg border-gray-300 focus:border-brand-primary focus:ring-brand-primary shadow-sm py-3 text-slate-900" placeholder="john@example.com">
                        @error('email') <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">{{ __('Mobile Number') }}</label>
                        <input type="text" wire:model="phone" class="w-full rounded-lg border-gray-300 focus:border-brand-primary focus:ring-brand-primary shadow-sm py-3 text-slate-900" placeholder="{{ __('e.g. 9876543210') }}">
                        @error('phone') <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-left">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">{{ __('Event Date') }}</label>
                <input type="date" wire:model="event_date" class="w-full rounded-lg border-gray-300 focus:border-brand-primary focus:ring-brand-primary shadow-sm py-3 text-slate-900">
                @error('event_date') <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">{{ __('Event Time') }}</label>
                <input type="time" wire:model="event_time" class="w-full rounded-lg border-gray-300 focus:border-brand-primary focus:ring-brand-primary shadow-sm py-3 text-slate-900">
                @error('event_time') <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">{{ __('Number of Guests') }}</label>
                <input type="number" wire:model="guest_count" class="w-full rounded-lg border-gray-300 focus:border-brand-primary focus:ring-brand-primary shadow-sm py-3 text-slate-900" placeholder="{{ __('e.g., 4') }}">
                @error('guest_count') <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">{{ __('Min Budget ($)') }}</label>
                    <input type="number" wire:model="budget_range_min" class="w-full rounded-lg border-gray-300 focus:border-brand-primary focus:ring-brand-primary shadow-sm py-3 text-slate-900" placeholder="100">
                    @error('budget_range_min') <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">{{ __('Max Budget ($)') }}</label>
                    <input type="number" wire:model="budget_range_max" class="w-full rounded-lg border-gray-300 focus:border-brand-primary focus:ring-brand-primary shadow-sm py-3 text-slate-900" placeholder="500">
                    @error('budget_range_max') <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <div class="pt-4 text-left">
            <label class="block text-sm font-semibold text-slate-700 mb-4">{{ __('Cuisine Preferences') }}</label>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach(['Indian', 'Chinese', 'Italian', 'Continental', 'Mexican', 'Thai'] as $cuisine)
                    <label class="inline-flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-slate-50 transition">
                        <input type="checkbox" wire:model="cuisine_preferences" value="{{ $cuisine }}" class="rounded border-gray-300 text-brand-primary focus:ring-brand-primary w-5 h-5">
                        <span class="ml-3 text-sm font-medium text-slate-700">{{ __($cuisine) }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="pt-4 text-left">
            <label class="block text-sm font-semibold text-slate-700 mb-2">{{ __('Additional Details') }}</label>
            <textarea wire:model="details" rows="5" class="w-full rounded-lg border-gray-300 focus:border-brand-primary focus:ring-brand-primary shadow-sm py-3 text-slate-900" placeholder="{{ __('Any dietary restrictions, specific requests, or equipment available in your kitchen?') }}"></textarea>
            @error('details') <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div class="pt-6">
            <button type="submit" class="w-full bg-brand-primary text-white px-8 py-4 rounded-full text-lg font-bold hover:bg-brand-primary-dark transition shadow-md">
                {{ __('Submit Request') }}
            </button>
        </div>
    </form>
</div>
