<div class="p-6 sm:p-10 {{ $isPage ? '' : 'max-h-[85vh] overflow-y-auto' }}">
    @if(!$isPage)
    <div class="text-center mb-8">
        <h2 class="text-[32px] font-light text-brand-primary mb-3">{{ __('Become a Partner Chef') }}</h2>
        <p class="text-[15px] text-slate-800">{{ __('Share your culinary expertise and start receiving booking requests.') }}</p>
    </div>
    @endif

    <form wire:submit.prevent="registerChef" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            
            <!-- Column 1: Account Details -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-slate-900 border-b border-gray-100 pb-2">{{ __('Account Credentials') }}</h3>
                
                <!-- Name -->
                <div>
                    <label for="chef_name" class="block text-sm font-medium text-slate-700 mb-1.5 px-2">{{ __('Full Name') }}</label>
                    <input wire:model="name" id="chef_name" type="text" required
                        class="block w-full px-6 py-3.5 border-gray-200 focus:border-brand-primary focus:ring-0 rounded-full shadow-sm placeholder-gray-400 text-[15px]"
                        placeholder="{{ __('John Doe') }}">
                    <x-input-error :messages="$errors->get('name')" class="mt-2 text-center" />
                </div>

                <!-- Email Address -->
                <div>
                    <label for="chef_email" class="block text-sm font-medium text-slate-700 mb-1.5 px-2">{{ __('Email Address') }}</label>
                    <input wire:model="email" id="chef_email" type="email" required
                        class="block w-full px-6 py-3.5 border-gray-200 focus:border-brand-primary focus:ring-0 rounded-full shadow-sm placeholder-gray-400 text-[15px]"
                        placeholder="{{ __('john@example.com') }}">
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-center" />
                </div>

                <!-- Phone Number -->
                <div>
                    <label for="chef_phone" class="block text-sm font-medium text-slate-700 mb-1.5 px-2">{{ __('Mobile Number') }}</label>
                    <input wire:model="phone" id="chef_phone" type="text" required
                        class="block w-full px-6 py-3.5 border-gray-200 focus:border-brand-primary focus:ring-0 rounded-full shadow-sm placeholder-gray-400 text-[15px]"
                        placeholder="{{ __('e.g. 9876543210') }}">
                    <x-input-error :messages="$errors->get('phone')" class="mt-2 text-center" />
                </div>

                <!-- Password -->
                <div>
                    <label for="chef_password" class="block text-sm font-medium text-slate-700 mb-1.5 px-2">{{ __('Password') }}</label>
                    <input wire:model="password" id="chef_password" type="password" required
                        class="block w-full px-6 py-3.5 border-gray-200 focus:border-brand-primary focus:ring-0 rounded-full shadow-sm placeholder-gray-400 text-[15px]"
                        placeholder="••••••••">
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-center" />
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="chef_password_confirmation" class="block text-sm font-medium text-slate-700 mb-1.5 px-2">{{ __('Confirm Password') }}</label>
                    <input wire:model="password_confirmation" id="chef_password_confirmation" type="password" required
                        class="block w-full px-6 py-3.5 border-gray-200 focus:border-brand-primary focus:ring-0 rounded-full shadow-sm placeholder-gray-400 text-[15px]"
                        placeholder="••••••••">
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-center" />
                </div>
            </div>

            <!-- Column 2: Professional Details -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-slate-900 border-b border-gray-100 pb-2">{{ __('Culinary Profile') }}</h3>

                <!-- Experience Years -->
                <div>
                    <label for="chef_experience" class="block text-sm font-medium text-slate-700 mb-1.5 px-2">{{ __('Years of Experience') }}</label>
                    <input wire:model="experience_years" id="chef_experience" type="number" min="0" required
                        class="block w-full px-6 py-3.5 border-gray-200 focus:border-brand-primary focus:ring-0 rounded-full shadow-sm placeholder-gray-400 text-[15px]"
                        placeholder="{{ __('e.g. 5') }}">
                    <x-input-error :messages="$errors->get('experience_years')" class="mt-2 text-center" />
                </div>

                <!-- Location -->
                <div>
                    <label for="chef_location" class="block text-sm font-medium text-slate-700 mb-1.5 px-2">{{ __('Location (City / Region)') }}</label>
                    <input wire:model="location" id="chef_location" type="text" required
                        class="block w-full px-6 py-3.5 border-gray-200 focus:border-brand-primary focus:ring-0 rounded-full shadow-sm placeholder-gray-400 text-[15px]"
                        placeholder="{{ __('e.g. Mumbai, Maharashtra') }}">
                    <x-input-error :messages="$errors->get('location')" class="mt-2 text-center" />
                </div>

                <!-- Specialties -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2 px-2">{{ __('Specialties (Select all that apply)') }}</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach(['Italian', 'French', 'Japanese', 'Mexican', 'Indian', 'Mediterranean', 'Pastry & Desserts', 'Vegan & Vegetarian'] as $specialty)
                            <label class="cursor-pointer">
                                <input type="checkbox" wire:model.live="specialties" value="{{ $specialty }}" class="sr-only">
                                <span class="inline-block px-4 py-2 text-xs font-semibold rounded-full border border-gray-200 transition-all duration-200 select-none {{ in_array($specialty, $specialties) ? 'bg-brand-primary border-brand-primary text-white shadow-sm' : 'bg-gray-50 text-slate-700 hover:bg-gray-100 hover:border-gray-300' }}">
                                    {{ __($specialty) }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                    <x-input-error :messages="$errors->get('specialties')" class="mt-2 text-center" />
                </div>
            </div>
        </div>

        <!-- Bio (Full Width) -->
        <div class="space-y-1">
            <label for="chef_bio" class="block text-sm font-medium text-slate-700 mb-1.5 px-2">{{ __('Professional Bio') }}</label>
            <textarea wire:model="bio" id="chef_bio" rows="4" required
                class="block w-full px-6 py-4 border-gray-200 focus:border-brand-primary focus:ring-0 rounded-3xl shadow-sm placeholder-gray-400 text-[15px] resize-none"
                placeholder="{{ __('Tell potential clients about your training, culinary philosophy, and signature dishes...') }}"></textarea>
            <x-input-error :messages="$errors->get('bio')" class="mt-2 text-center" />
        </div>

        <!-- Submit Button -->
        <div class="pt-4 flex justify-center">
            <button type="submit" class="w-full bg-brand-primary text-white font-bold text-[15px] py-3.5 rounded-full hover:bg-brand-primary-dark transition shadow-sm">
                {{ __('Register as Chef') }}
            </button>
        </div>
    </form>
</div>
