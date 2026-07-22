<div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-4">
        <h3 class="text-3xl font-serif font-bold text-slate-900">{{ __('My Service Requests') }}</h3>
        <a href="{{ route('submit-request') }}" class="bg-brand-primary text-white px-6 py-2.5 rounded-full text-sm font-bold hover:bg-brand-primary-dark transition shadow-sm whitespace-nowrap">
            {{ __('New Request') }}
        </a>
    </div>

    @if($requests->isEmpty())
        <div class="bg-white rounded-2xl p-16 text-center shadow-sm border border-gray-100">
            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
            </div>
            <p class="text-lg text-slate-600 mb-6">{{ __('You haven\'t submitted any requests yet.') }}</p>
            <a href="{{ route('submit-request') }}" class="inline-block border-2 border-slate-900 text-slate-900 font-bold px-8 py-3 rounded-full hover:bg-slate-50 transition">{{ __('Start your first request') }}</a>
        </div>
    @else
        <div class="grid gap-6">
            @foreach($requests as $request)
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:shadow-md transition" x-data="{ showDetails: false }">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                                    @if($request->status == 'open') bg-green-100 text-green-700 @else bg-gray-100 text-gray-700 @endif">
                                    {{ __(ucfirst($request->status)) }}
                                </span>
                                <span class="text-sm text-slate-500 font-medium">
                                    {{ $request->created_at->diffForHumans() }}
                                </span>
                            </div>
                            <h4 class="text-xl font-bold text-slate-900">
                                {{ __('Event on') }} {{ $request->event_date->format('M d, Y') }} {{ __('at') }} {{ \Carbon\Carbon::parse($request->event_time)->format('g:i A') }}
                            </h4>
                            <p class="text-slate-600 mt-2 font-medium">
                                {{ $request->guest_count }} {{ __('Guests') }} 
                                @if($request->cuisine_preferences)
                                    • {{ implode(', ', $request->cuisine_preferences) }}
                                @endif
                            </p>
                        </div>
                        
                        <div class="w-full md:w-auto text-left md:text-right">
                            <p class="text-3xl font-serif font-bold text-brand-primary mb-1">
                                {{ $request->proposals()->count() }}
                            </p>
                            <p class="text-sm text-slate-500 font-medium uppercase tracking-wider mb-4">{{ __('Proposals received') }}</p>
                            
                            <button @click="showDetails = !showDetails" class="inline-block w-full md:w-auto text-center border-2 border-slate-900 text-slate-900 text-sm font-bold px-6 py-2 rounded-full hover:bg-slate-50 transition focus:outline-none select-none">
                                <span x-show="!showDetails">{{ __('View Details') }}</span>
                                <span x-show="showDetails" x-cloak>{{ __('Hide Details') }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- Expandable Details Block -->
                    <div x-show="showDetails" x-collapse class="mt-6 pt-6 border-t border-gray-100 space-y-6" x-cloak>
                        <div>
                            <h5 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">{{ __('Request Details') }}</h5>
                            <p class="text-slate-700 bg-slate-50 p-4 rounded-xl text-[15px] leading-relaxed italic">
                                {{ $request->details ?: __('No additional details provided.') }}
                            </p>
                        </div>

                        @php
                            $acceptedProposal = $request->proposals()->where('status', 'accepted')->first();
                            $assignedChef = $acceptedProposal ? $acceptedProposal->chef : null;
                        @endphp

                        @if($assignedChef)
                            <div class="bg-amber-50/50 border border-amber-100 rounded-2xl p-6 space-y-4 text-left">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-brand-primary/10 rounded-full flex items-center justify-center text-brand-primary">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    </div>
                                    <div>
                                        <h5 class="text-lg font-bold text-slate-900">{{ __('Your Assigned Chef') }}</h5>
                                        <p class="text-sm text-slate-500">{{ __('Chef status confirmed and booking active.') }}</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                    <div class="space-y-2">
                                        <p><span class="font-semibold text-slate-600">{{ __('Name:') }}</span> <span class="text-slate-900 font-medium">{{ $assignedChef->name }}</span></p>
                                        <p><span class="font-semibold text-slate-600">{{ __('Email:') }}</span> <span class="text-slate-900 font-medium">{{ $assignedChef->email }}</span></p>
                                        <p><span class="font-semibold text-slate-600">{{ __('Mobile:') }}</span> <span class="text-slate-900 font-medium">{{ $assignedChef->phone ?: __('Not Provided') }}</span></p>
                                    </div>
                                    <div class="space-y-2">
                                        <p><span class="font-semibold text-slate-600">{{ __('Location:') }}</span> <span class="text-slate-900 font-medium">{{ $assignedChef->chefProfile?->location ?: __('Not Provided') }}</span></p>
                                        <p><span class="font-semibold text-slate-600">{{ __('Experience:') }}</span> <span class="text-slate-900 font-medium">{{ $assignedChef->chefProfile?->experience_years ? $assignedChef->chefProfile->experience_years . ' ' . __('Years') : __('Not Provided') }}</span></p>
                                        <p>
                                            <span class="font-semibold text-slate-600">{{ __('Specialties:') }}</span> 
                                            <span class="text-slate-900 font-medium">
                                                {{ $assignedChef->chefProfile?->specialties ? implode(', ', $assignedChef->chefProfile->specialties) : __('Not Provided') }}
                                            </span>
                                        </p>
                                    </div>
                                </div>

                                @if($assignedChef->chefProfile?->bio)
                                    <div class="pt-2">
                                        <span class="block font-semibold text-slate-600 text-sm mb-1">{{ __('Chef Bio:') }}</span>
                                        <p class="text-slate-700 bg-white border border-gray-100 p-3 rounded-xl text-[14px] leading-relaxed">
                                            {{ $assignedChef->chefProfile->bio }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="bg-slate-50 border border-gray-100 rounded-2xl p-6 text-center text-sm text-slate-500">
                                <svg class="w-8 h-8 mx-auto mb-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                {{ __('We are matching your request with qualified private chefs. Once a chef is assigned, you will see their details and contact information here.') }}
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
