<div>
    <!-- Success / Error Alerts -->
    @if(session()->has('success'))
        <div class="mb-8 bg-green-50 border border-green-200 text-green-800 rounded-2xl p-4 flex items-center gap-3 shadow-sm max-w-3xl mx-auto">
            <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="text-sm font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Premium Dashboard Tabs -->
    <div class="flex border-b border-gray-200 mb-10 max-w-3xl mx-auto gap-2 md:gap-4 overflow-x-auto pb-px">
        <button wire:click="$set('activeTab', 'bookings')" 
            class="flex items-center gap-2 py-3 px-4 md:px-6 font-bold text-sm tracking-wide border-b-2 transition focus:outline-none whitespace-nowrap select-none
            {{ $activeTab === 'bookings' ? 'border-brand-primary text-brand-primary' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            {{ __('My Bookings') }}
            <span class="ml-1.5 px-2 py-0.5 text-xs bg-slate-100 text-slate-600 rounded-full font-bold">{{ $assignedRequests->count() }}</span>
        </button>

        <button wire:click="$set('activeTab', 'requests')" 
            class="flex items-center gap-2 py-3 px-4 md:px-6 font-bold text-sm tracking-wide border-b-2 transition focus:outline-none whitespace-nowrap select-none
            {{ $activeTab === 'requests' ? 'border-brand-primary text-brand-primary' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            {{ __('Open Requests') }}
            <span class="ml-1.5 px-2 py-0.5 text-xs bg-slate-100 text-slate-600 rounded-full font-bold">{{ $openRequests->count() }}</span>
        </button>

        <button wire:click="$set('activeTab', 'account')" 
            class="flex items-center gap-2 py-3 px-4 md:px-6 font-bold text-sm tracking-wide border-b-2 transition focus:outline-none whitespace-nowrap select-none
            {{ $activeTab === 'account' ? 'border-brand-primary text-brand-primary' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            {{ __('My Account') }}
        </button>
    </div>

    <!-- Active Tab Panel Content -->
    <div>
        <!-- 1. Assigned Bookings Panel -->
        @if($activeTab === 'bookings')
            <div class="mb-14 text-left">
                <h3 class="text-3xl font-serif font-bold mb-6 text-slate-900">{{ __('My Assigned Bookings') }}</h3>
                
                @if($assignedRequests->isEmpty())
                    <div class="bg-white rounded-2xl p-16 text-center shadow-sm border border-gray-100">
                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        </div>
                        <p class="text-slate-600 font-medium">{{ __('You have no active assigned bookings.') }}</p>
                        <p class="text-sm text-slate-500 mt-1">{{ __('Submit proposals for open requests to get hired!') }}</p>
                    </div>
                @else
                    <div class="grid gap-6">
                        @foreach($assignedRequests as $request)
                            <div class="bg-amber-50/40 border border-amber-100 rounded-2xl p-8 shadow-sm hover:shadow-md transition">
                                <div class="flex flex-col md:flex-row justify-between items-start">
                                    <div class="flex-grow">
                                        <div class="flex items-center gap-3 mb-3">
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 uppercase tracking-wider">
                                                {{ __('Assigned') }}
                                            </span>
                                            <h4 class="text-xl font-bold text-slate-900">
                                                {{ __('Client:') }} {{ $request->user->name }}
                                            </h4>
                                        </div>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mb-4 text-slate-600 text-sm font-medium">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-brand-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                {{ $request->event_date->format('M d, Y') }} {{ __('at') }} {{ \Carbon\Carbon::parse($request->event_time)->format('g:i A') }}
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-brand-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                                {{ $request->guest_count }} {{ __('Guests') }}
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-brand-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                                {{ $request->user->phone ?: __('No Mobile Provided') }}
                                            </div>
                                        </div>

                                        <p class="text-slate-600 leading-relaxed mb-4 text-sm bg-white border border-gray-100 p-4 rounded-xl italic">
                                            {{ $request->details ?: __('No additional details provided.') }}
                                        </p>

                                        <div class="flex flex-wrap gap-2">
                                            @if($request->cuisine_preferences)
                                                @foreach($request->cuisine_preferences as $cuisine)
                                                    <span class="bg-amber-100/60 text-amber-800 px-3 py-1 rounded-full text-xs font-semibold tracking-wide">
                                                        {{ __($cuisine) }}
                                                    </span>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

        <!-- 2. Open Requests Panel -->
        @if($activeTab === 'requests')
            <div class="text-left">
                <h3 class="text-3xl font-serif font-bold mb-6 text-slate-900">{{ __('Open Service Requests') }}</h3>

                @if($openRequests->isEmpty())
                    <div class="bg-white rounded-2xl p-16 text-center shadow-sm border border-gray-100">
                        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <p class="text-lg text-slate-600 mb-2">{{ __('No open requests at the moment.') }}</p>
                        <p class="text-sm text-slate-500">{{ __('Check back later for new booking opportunities.') }}</p>
                    </div>
                @else
                    <div class="grid gap-6">
                        @foreach($openRequests as $request)
                            <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:shadow-md transition">
                                <div class="flex flex-col md:flex-row justify-between items-start">
                                    <div class="flex-1 col-span-2">
                                        <div class="flex items-center gap-3 mb-3">
                                            <h4 class="text-xl font-bold text-slate-900">
                                                {{ __('Request by') }} {{ $request->user->name }}
                                            </h4>
                                            <span class="text-sm text-slate-500 font-medium">
                                                {{ $request->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                        
                                        <div class="grid grid-cols-2 gap-4 mb-4 text-slate-600 text-sm font-medium">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-brand-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                {{ $request->event_date->format('M d, Y') }} {{ __('at') }} {{ \Carbon\Carbon::parse($request->event_time)->format('g:i A') }}
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-brand-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                                {{ $request->guest_count }} {{ __('Guests') }}
                                            </div>
                                        </div>

                                        <p class="text-slate-600 line-clamp-2 leading-relaxed mb-4">
                                            {{ $request->details }}
                                        </p>

                                        <div class="flex flex-wrap gap-2">
                                            @if($request->cuisine_preferences)
                                                @foreach($request->cuisine_preferences as $cuisine)
                                                    <span class="bg-gray-100 text-slate-600 px-3 py-1 rounded-full text-xs font-semibold tracking-wide">
                                                        {{ __($cuisine) }}
                                                    </span>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>

                                    <div class="mt-6 md:mt-0 md:ml-8 text-left md:text-right flex flex-col justify-between h-full min-w-[150px]">
                                        <div class="mb-4 md:mb-0">
                                            <p class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-1">{{ __('Budget') }}</p>
                                            <p class="text-2xl font-serif font-bold text-slate-900">
                                                @if($request->budget_range_min)
                                                    ${{ number_format($request->budget_range_min) }} <span class="text-lg font-sans text-slate-500 font-normal">-</span> ${{ number_format($request->budget_range_max) }}
                                                @else
                                                    {{ __('Open') }}
                                                @endif
                                            </p>
                                        </div>
                                        
                                        <a href="#" class="inline-block w-full md:w-auto text-center bg-brand-primary text-white px-6 py-2.5 rounded-full text-sm font-bold hover:bg-brand-primary-dark transition shadow-sm mt-4 md:mt-0">
                                            {{ __('Submit Proposal') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

        <!-- 3. Account Settings Panel -->
        @if($activeTab === 'account')
            <div class="max-w-4xl mx-auto text-left">
                <h3 class="text-3xl font-serif font-bold mb-6 text-slate-900">{{ __('Account Settings') }}</h3>

                <form wire:submit.prevent="saveProfile" class="bg-white rounded-3xl p-8 md:p-10 shadow-sm border border-gray-100 space-y-8">
                    <!-- Top Section: Profile Picture -->
                    <div class="flex flex-col sm:flex-row items-center gap-6 pb-6 border-b border-gray-100">
                        <div class="relative w-28 h-28 flex-shrink-0">
                            @if($avatar)
                                <img src="{{ $avatar->temporaryUrl() }}" class="w-full h-full object-cover rounded-full border border-gray-200">
                            @elseif($currentAvatarUrl)
                                <img src="/storage/{{ $currentAvatarUrl }}" class="w-full h-full object-cover rounded-full border border-gray-200">
                            @else
                                <div class="w-full h-full bg-slate-100 rounded-full flex items-center justify-center text-brand-primary text-3xl font-bold font-serif border border-gray-200 uppercase">
                                    {{ substr($name, 0, 1) }}
                                </div>
                            @endif
                            <!-- Uploading Indicator -->
                            <div wire:loading wire:target="avatar" class="absolute inset-0 bg-black/40 backdrop-blur-sm rounded-full flex items-center justify-center">
                                <svg class="animate-spin h-6 w-6 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            </div>
                        </div>
                        <div class="text-center sm:text-left">
                            <h4 class="text-lg font-bold text-slate-900 mb-1">{{ __('Profile Picture') }}</h4>
                            <p class="text-sm text-slate-500 mb-3">{{ __('Upload a professional portrait to stand out on the partner chefs page.') }}</p>
                            
                            <label class="inline-block bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold px-5 py-2 rounded-full text-xs transition cursor-pointer select-none">
                                <span>{{ __('Choose Photo') }}</span>
                                <input type="file" wire:model="avatar" class="hidden" accept="image/*">
                            </label>
                            <x-input-error :messages="$errors->get('avatar')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Grid Layout for Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div>
                            <label for="profile_name" class="block text-sm font-bold text-slate-700 mb-1.5 px-2">{{ __('Full Name') }}</label>
                            <input wire:model="name" id="profile_name" type="text" required
                                class="block w-full px-6 py-3 border border-gray-200 focus:border-brand-primary focus:ring-0 rounded-full shadow-sm placeholder-gray-400 text-[15px]"
                                placeholder="Your Name">
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="profile_email" class="block text-sm font-bold text-slate-700 mb-1.5 px-2">{{ __('Email Address') }}</label>
                            <input wire:model="email" id="profile_email" type="email" required
                                class="block w-full px-6 py-3 border border-gray-200 focus:border-brand-primary focus:ring-0 rounded-full shadow-sm placeholder-gray-400 text-[15px]"
                                placeholder="chef@example.com">
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Mobile Number -->
                        <div>
                            <label for="profile_phone" class="block text-sm font-bold text-slate-700 mb-1.5 px-2">{{ __('Mobile Number') }}</label>
                            <input wire:model="phone" id="profile_phone" type="text" required
                                class="block w-full px-6 py-3 border border-gray-200 focus:border-brand-primary focus:ring-0 rounded-full shadow-sm placeholder-gray-400 text-[15px]"
                                placeholder="e.g. 9876543210">
                            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                        </div>

                        <!-- Location -->
                        <div>
                            <label for="profile_location" class="block text-sm font-bold text-slate-700 mb-1.5 px-2">{{ __('Location (City / Region)') }}</label>
                            <input wire:model="location" id="profile_location" type="text" required
                                class="block w-full px-6 py-3 border border-gray-200 focus:border-brand-primary focus:ring-0 rounded-full shadow-sm placeholder-gray-400 text-[15px]"
                                placeholder="e.g. Mumbai, Maharashtra">
                            <x-input-error :messages="$errors->get('location')" class="mt-2" />
                        </div>

                        <!-- Experience -->
                        <div>
                            <label for="profile_experience" class="block text-sm font-bold text-slate-700 mb-1.5 px-2">{{ __('Years of Experience') }}</label>
                            <input wire:model="experience_years" id="profile_experience" type="number" min="0" required
                                class="block w-full px-6 py-3 border border-gray-200 focus:border-brand-primary focus:ring-0 rounded-full shadow-sm placeholder-gray-400 text-[15px]">
                            <x-input-error :messages="$errors->get('experience_years')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Specialties (Checkboxes grid) -->
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-3 px-2">{{ __('Specialties / Cuisines') }}</label>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach($availableSpecialties as $spec)
                                <label class="flex items-center gap-2.5 px-4 py-2.5 bg-slate-50 border border-slate-100 rounded-full cursor-pointer hover:bg-slate-100/70 transition select-none">
                                    <input type="checkbox" wire:model="specialties" value="{{ $spec }}" class="text-brand-primary focus:ring-0 rounded-full border-gray-200">
                                    <span class="text-sm font-semibold text-slate-700">{{ __($spec) }}</span>
                                </label>
                            @endforeach
                        </div>
                        <x-input-error :messages="$errors->get('specialties')" class="mt-2" />
                    </div>

                    <!-- Professional Bio -->
                    <div>
                        <label for="profile_bio" class="block text-sm font-bold text-slate-700 mb-1.5 px-2">{{ __('Professional Bio') }}</label>
                        <textarea wire:model="bio" id="profile_bio" rows="6" required
                            class="block w-full px-6 py-4 border border-gray-200 focus:border-brand-primary focus:ring-0 rounded-2xl shadow-sm placeholder-gray-400 text-[15px] resize-none"
                            placeholder="{{ __('Write about your culinary background, style, signature dishes, and overall experience...') }}"></textarea>
                        <x-input-error :messages="$errors->get('bio')" class="mt-2" />
                    </div>

                    <!-- Actions -->
                    <div class="pt-6 border-t border-gray-100 flex justify-end gap-3">
                        <button type="submit" class="bg-brand-primary hover:bg-brand-primary-dark text-white font-bold px-8 py-3.5 rounded-full text-[15px] transition shadow-md focus:outline-none select-none">
                            {{ __('Save Changes') }}
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>
