<div class="bg-white p-8 md:p-12 rounded-2xl shadow-sm border border-gray-100">
    <h2 class="text-2xl font-serif font-bold mb-8 text-slate-900">{{ __('Submit Your Event Requirements') }}</h2>

    <form wire:submit.prevent="submit" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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

        <div class="pt-4">
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

        <div class="pt-4">
            <label class="block text-sm font-semibold text-slate-700 mb-2">{{ __('Additional Details') }}</label>
            <textarea wire:model="details" rows="5" class="w-full rounded-lg border-gray-300 focus:border-brand-primary focus:ring-brand-primary shadow-sm py-3 text-slate-900" placeholder="{{ __('Any dietary restrictions, specific requests, or equipment available in your kitchen?') }}"></textarea>
            @error('details') <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div class="pt-6">
            <button type="submit" class="w-full bg-brand-primary text-white px-8 py-4 rounded-lg text-lg font-bold hover:bg-brand-primary-dark transition shadow-md">
                {{ __('Submit Request') }}
            </button>
        </div>
    </form>
</div>
