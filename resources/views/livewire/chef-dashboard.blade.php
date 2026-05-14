<div>
    <h3 class="text-3xl font-serif font-bold mb-10 text-slate-900">{{ __('Open Service Requests') }}</h3>

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
                        <div class="flex-1">
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
