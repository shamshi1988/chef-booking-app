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
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:shadow-md transition">
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
                            
                            <a href="#" class="inline-block w-full md:w-auto text-center border-2 border-slate-900 text-slate-900 text-sm font-bold px-6 py-2 rounded-full hover:bg-slate-50 transition">
                                {{ __('View Details') }}
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
