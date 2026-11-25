<div>
    <div class="dash-head">
        <div>
            <h1>{{ __('My Bookings') }}</h1>
            <p class="muted">{{ __('Track your service requests and see your assigned professional.') }}</p>
        </div>
        <a href="{{ route('book') }}" class="btn btn-primary">{{ __('Book a Service') }}</a>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success" style="margin-bottom:20px;">{{ session('success') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-error" style="margin-bottom:20px;">{{ session('error') }}</div>
    @endif

    {{-- Ask the customer to rate completed services --}}
    @if ($awaitingRating->isNotEmpty())
        <div class="rate-prompt">
            <span class="rate-prompt-icon">★</span>
            <div style="flex:1">
                <strong>{{ trans_choice('{1} You have 1 completed service to rate|[2,*] You have :count completed services to rate', $awaitingRating->count(), ['count' => $awaitingRating->count()]) }}</strong>
                <p>{{ __('Your rating helps other customers and helps us keep quality high.') }}</p>
            </div>
            <button type="button" class="btn btn-primary btn-sm" wire:click="openRating({{ $awaitingRating->first()->id }})">{{ __('Rate now') }}</button>
        </div>
    @endif

    @if ($requests->isEmpty())
        <div class="card empty">
            <span class="icon-badge lg"><x-ui-icon name="calendar" /></span>
            <p>{{ __("You haven't booked any services yet.") }}</p>
            <a href="{{ route('services.index') }}" class="btn btn-primary">{{ __('Browse services') }}</a>
        </div>
    @else
        <div class="list">
            @foreach ($requests as $request)
                @php
                    $booking = $request->booking;
                    $provider = $booking?->proposal?->provider;
                @endphp
                <div class="card req-card" wire:key="req-{{ $request->id }}">
                    <div class="req-top">
                        <div class="req-title">
                            <span class="icon-badge"><x-ui-icon :name="$request->category?->icon ?? 'home'" /></span>
                            <div>
                                <h3>{{ __($request->category?->name ?? 'Service request') }}</h3>
                                <p class="muted small">{{ __('Booked') }} {{ $request->created_at->diffForHumans() }} · #{{ $request->id }}</p>
                            </div>
                        </div>
                        <span class="badge badge-{{ $request->status }}">{{ $request->statusLabel() }}</span>
                    </div>

                    @include('livewire.partials.request-details', ['request' => $request, 'showContact' => true])

                    @if ($provider)
                        <div class="note" style="background:var(--success-soft);">
                            <strong style="color:var(--ink);">{{ __('Your professional') }}:</strong>
                            {{ $provider->name }}
                            @if ($provider->phone) · <a class="link" href="tel:{{ $provider->phone }}">{{ $provider->phone }}</a>@endif
                        </div>

                        @if ($booking->status === 'completed')
                            @if ($booking->rating)
                                <div class="rated">
                                    <span class="stars">{{ str_repeat('★', $booking->rating->rating) }}<span style="color:var(--line)">{{ str_repeat('★', 5 - $booking->rating->rating) }}</span></span>
                                    <span class="muted small">{{ __('Your rating') }}</span>
                                    @if ($booking->rating->comment)<p>“{{ $booking->rating->comment }}”</p>@endif
                                </div>
                            @else
                                <div class="rate-row">
                                    <span class="small">{{ __('How was :name’s service?', ['name' => $provider->name]) }}</span>
                                    <button type="button" class="btn btn-primary btn-sm" wire:click="openRating({{ $booking->id }})">★ {{ __('Rate your professional') }}</button>
                                </div>
                            @endif
                        @endif
                    @elseif ($request->status === 'open')
                        <div style="display:flex;flex-wrap:wrap;gap:12px;justify-content:space-between;align-items:center;">
                            <p class="muted small">{{ __('We are finding the best professional for you and will call you to confirm.') }}</p>
                            <button type="button" class="btn btn-outline btn-sm" wire:click="cancelRequest({{ $request->id }})" wire:confirm="{{ __('Cancel this booking request?') }}">{{ __('Cancel request') }}</button>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    {{-- Rating pop-up --}}
    @if ($ratingBooking)
        <div class="modal-backdrop" wire:click.self="closeRating">
            <div class="modal" role="dialog" aria-modal="true" aria-labelledby="rate-title">
                <div class="modal-head">
                    <h3 id="rate-title" style="font-size:20px;">{{ __('Rate your professional') }}</h3>
                    <button type="button" class="icon-btn" wire:click="closeRating" aria-label="{{ __('Close') }}"><x-ui-icon name="x" /></button>
                </div>
                <p class="muted small" style="margin-bottom:18px;">
                    {{ $ratingBooking->proposal->provider->name }} ·
                    {{ __($ratingBooking->proposal->serviceRequest->category?->name ?? 'Service') }} ·
                    {{ $ratingBooking->proposal->serviceRequest->preferred_date?->translatedFormat('d M Y') }}
                </p>
                <form wire:submit="submitRating" class="form">
                    <div @class(['field', 'has-error' => $errors->has('ratingValue')])>
                        <span class="label">{{ __('Your rating') }}</span>
                        <div class="star-pick">
                            @for ($i = 1; $i <= 5; $i++)
                                <button type="button" wire:click="$set('ratingValue', {{ $i }})" @class(['on' => $i <= $ratingValue]) aria-label="{{ $i }} {{ __('stars') }}">★</button>
                            @endfor
                        </div>
                        @error('ratingValue') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div @class(['field', 'has-error' => $errors->has('commentText')])>
                        <label class="label" for="rating_comment">{{ __('Your review') }} <span class="muted small">({{ __('Optional') }})</span></label>
                        <textarea id="rating_comment" wire:model="commentText" class="textarea" placeholder="{{ __('How was the service?') }}"></textarea>
                        @error('commentText') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <button type="submit" class="btn btn-primary btn-block" wire:loading.attr="disabled" wire:target="submitRating">{{ __('Submit rating') }}</button>
                </form>
            </div>
        </div>
    @endif
</div>
