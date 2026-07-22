<div class="py-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center max-w-3xl mx-auto mb-16">
        <h1 class="text-4xl md:text-5xl font-serif font-bold text-slate-900 mb-4">{{ __('Our Partner Chefs') }}</h1>
        <p class="text-lg text-slate-600 leading-relaxed">{{ __('Meet our highly experienced private chefs who are ready to create a unique culinary experience right in your home.') }}</p>
    </div>

    <!-- Session Alert Notifications -->
    @if(session()->has('success'))
        <div class="mb-8 max-w-3xl mx-auto bg-green-50 border border-green-200 text-green-800 rounded-2xl p-4 flex items-center gap-3">
            <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif
    @if(session()->has('error'))
        <div class="mb-8 max-w-3xl mx-auto bg-red-50 border border-red-200 text-red-800 rounded-2xl p-4 flex items-center gap-3">
            <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="text-sm font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Chefs Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($chefs as $chef)
            <div class="bg-white rounded-3xl overflow-hidden shadow-sm border border-gray-100 hover:shadow-lg transition duration-300 flex flex-col h-full">
                <!-- Header gradient block -->
                <div class="h-24 bg-gradient-to-r from-brand-primary/10 to-brand-primary/30 relative"></div>
                
                <!-- Chef Info Body -->
                <div class="px-8 pb-8 pt-0 flex-grow flex flex-col relative -mt-12 text-left">
                    <!-- Avatar circle -->
                    <div class="w-24 h-24 bg-white rounded-full p-1.5 shadow-sm mb-4 overflow-hidden">
                        @if($chef->chefProfile?->avatar_url)
                            <img src="/storage/{{ $chef->chefProfile->avatar_url }}" alt="{{ $chef->name }}" class="w-full h-full object-cover rounded-full">
                        @else
                            <div class="w-full h-full bg-slate-100 rounded-full flex items-center justify-center text-brand-primary text-3xl font-bold font-serif border border-gray-100 uppercase">
                                {{ substr($chef->name, 0, 1) }}
                            </div>
                        @endif
                    </div>

                    <!-- Rating stars -->
                    @php
                        $avg = $chef->averageRating();
                        $reviewsCount = $chef->ratingsReceived()->count();
                    @endphp
                    <div class="flex items-center gap-1.5 mb-2">
                        @if($reviewsCount > 0)
                            <div class="flex text-amber-400">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 {{ $i <= round($avg) ? 'fill-current' : 'stroke-current fill-none' }}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.907c.961 0 1.36 1.252.588 1.81l-3.97 2.883a1 1 0 00-.364 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.97-2.883a1 1 0 00-1.175 0l-3.97 2.883c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.364-1.118L2.98 10.1c-.773-.558-.375-1.81.587-1.81h4.907a1 1 0 00.95-.69l1.52-4.674z"></path></svg>
                                @endfor
                            </div>
                            <span class="text-sm font-bold text-slate-800">{{ $avg }}</span>
                            <span class="text-xs text-slate-500 font-medium">({{ $reviewsCount }} {{ $reviewsCount == 1 ? __('review') : __('reviews') }})</span>
                        @else
                            <span class="text-xs text-slate-400 font-semibold uppercase tracking-wider">{{ __('No ratings yet') }}</span>
                        @endif
                    </div>

                    <h3 class="text-xl font-bold text-slate-900 mb-1">{{ $chef->name }}</h3>
                    <p class="text-xs text-slate-500 font-semibold mb-4 uppercase tracking-wider flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        {{ $chef->chefProfile?->location ?: __('Not Specified') }}
                        @if($chef->chefProfile?->experience_years)
                            • {{ $chef->chefProfile->experience_years }} {{ __('Years Exp.') }}
                        @endif
                    </p>

                    @if($chef->chefProfile?->bio)
                        <p class="text-slate-600 text-sm leading-relaxed mb-6 line-clamp-3">
                            {{ $chef->chefProfile->bio }}
                        </p>
                    @endif

                    <!-- Specialties list -->
                    @if($chef->chefProfile?->specialties)
                        <div class="flex flex-wrap gap-1.5 mb-6">
                            @foreach($chef->chefProfile->specialties as $specialty)
                                <span class="bg-slate-50 text-slate-600 border border-slate-100 px-2.5 py-0.5 rounded-full text-xs font-semibold tracking-wide">
                                    {{ __($specialty) }}
                                </span>
                            @endforeach
                        </div>
                    @endif

                    <div class="mt-auto pt-4 border-t border-gray-100 flex items-center justify-between gap-2">
                        <div class="flex flex-col">
                            <span class="text-xs text-slate-400 font-semibold uppercase tracking-wider">{{ __('Verified Partner') }}</span>
                            @if($this->canRateChef($chef))
                                <button wire:click="openRatingModal({{ $chef->id }})" class="text-brand-primary hover:text-brand-primary-dark text-xs font-bold mt-1 text-left focus:outline-none select-none">
                                    {{ __('Rate Chef') }}
                                </button>
                            @endif
                        </div>
                        
                        <a href="{{ route('submit-request', ['chef_id' => $chef->id]) }}" class="bg-brand-primary hover:bg-brand-primary-dark text-white px-5 py-2.5 rounded-full text-xs font-bold transition shadow-sm select-none">
                            {{ __('Book Chef') }}
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Rating Submission Modal -->
    @if($showRatingModal)
        <div x-data="{ show: @entangle('showRatingModal') }" x-show="show" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-cloak style="display: none;">
            <div @click.away="show = false" class="bg-white w-full max-w-lg rounded-3xl shadow-xl overflow-hidden transform transition-all p-8 border border-gray-100 text-left">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-serif font-bold text-slate-900">{{ __('Rate Chef') }}</h3>
                    <button @click="show = false" class="text-slate-400 hover:text-slate-600 transition focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form wire:submit.prevent="submitRating" class="space-y-6">
                    <!-- Stars Selector -->
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">{{ __('How was your experience?') }}</label>
                        <div class="flex gap-2">
                            @for($i = 1; $i <= 5; $i++)
                                <button type="button" wire:click="$set('ratingValue', {{ $i }})" class="focus:outline-none transition transform hover:scale-110">
                                    <svg class="w-10 h-10 {{ $i <= $ratingValue ? 'text-amber-400 fill-current' : 'text-slate-200 fill-none stroke-current stroke-2' }}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.907c.961 0 1.36 1.252.588 1.81l-3.97 2.883a1 1 0 00-.364 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.97-2.883a1 1 0 00-1.175 0l-3.97 2.883c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.364-1.118L2.98 10.1c-.773-.558-.375-1.81.587-1.81h4.907a1 1 0 00.95-.69l1.52-4.674z"></path></svg>
                                </button>
                            @endfor
                        </div>
                    </div>

                    <!-- Comment Textarea -->
                    <div>
                        <label for="rating_comment" class="block text-sm font-bold text-slate-700 mb-1.5">{{ __('Review Comments') }}</label>
                        <textarea wire:model="commentText" id="rating_comment" rows="4" required
                            class="block w-full px-5 py-3 border border-gray-200 focus:border-brand-primary focus:ring-0 rounded-2xl shadow-sm placeholder-gray-400 text-[15px] resize-none"
                            placeholder="{{ __('Write about the food quality, professionalism, and experience...') }}"></textarea>
                        <x-input-error :messages="$errors->get('commentText')" class="mt-2" />
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <button type="button" @click="show = false" class="border-2 border-slate-200 text-slate-600 px-6 py-2.5 rounded-full text-sm font-bold hover:bg-slate-50 transition focus:outline-none">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit" class="bg-brand-primary hover:bg-brand-primary-dark text-white px-8 py-2.5 rounded-full text-sm font-bold transition shadow-md focus:outline-none">
                            {{ __('Submit Review') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
