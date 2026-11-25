<div>
    <div class="dash-head">
        <div>
            <h1>{{ __('Hello, :name', ['name' => auth()->user()->name]) }}</h1>
            <p class="muted">
                {{ $profile?->category ? __($profile->category->name) . ' ' . __('professional') : __('Professional') }}
                @if ($profile?->is_verified)
                    <span class="badge badge-verified">✓ {{ __('Verified') }}</span>
                @else
                    <span class="badge badge-open">{{ __('Verification pending') }}</span>
                @endif
            </p>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success" style="margin-bottom:20px;">{{ session('success') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-error" style="margin-bottom:20px;">{{ session('error') }}</div>
    @endif

    <div class="tabs" role="tablist">
        <button type="button" wire:click="setTab('jobs')" @class(['is-active' => $activeTab === 'jobs'])>{{ __('My Jobs') }} <span class="count">{{ $assignedRequests->count() + $directRequests->count() }}</span></button>
        <button type="button" wire:click="setTab('requests')" @class(['is-active' => $activeTab === 'requests'])>{{ __('Open Requests') }} <span class="count">{{ $openRequests->count() }}</span></button>
        <button type="button" wire:click="setTab('account')" @class(['is-active' => $activeTab === 'account'])>{{ __('Profile') }}</button>
    </div>

    {{-- My jobs --}}
    @if ($activeTab === 'jobs')
        <div class="list">
            @foreach ($directRequests as $proposal)
                @php $req = $proposal->serviceRequest; @endphp
                <div class="card req-card" wire:key="direct-{{ $proposal->id }}" style="border-color:var(--primary);">
                    <div class="req-top">
                        <div class="req-title">
                            <span class="icon-badge"><x-ui-icon :name="$req->category?->icon ?? 'home'" /></span>
                            <div>
                                <span class="badge badge-primary">{{ __('New booking request for you') }}</span>
                                <h3 style="margin-top:6px;">{{ __($req->category?->name ?? 'Service') }} – {{ $req->city }}</h3>
                            </div>
                        </div>
                        <div style="display:flex;gap:8px;">
                            <button type="button" class="btn btn-primary btn-sm" wire:click="acceptRequest({{ $proposal->id }})" wire:loading.attr="disabled">{{ __('Accept') }}</button>
                            <button type="button" class="btn btn-outline btn-sm" wire:click="declineRequest({{ $proposal->id }})" wire:confirm="{{ __('Decline this booking request?') }}" wire:loading.attr="disabled">{{ __('Decline') }}</button>
                        </div>
                    </div>
                    @include('livewire.partials.request-details', ['request' => $req, 'showContact' => false])
                </div>
            @endforeach

            @forelse ($assignedRequests as $req)
                <div class="card req-card" wire:key="job-{{ $req->id }}">
                    <div class="req-top">
                        <div class="req-title">
                            <span class="icon-badge"><x-ui-icon :name="$req->category?->icon ?? 'home'" /></span>
                            <div>
                                <h3>{{ __($req->category?->name ?? 'Service') }}</h3>
                                <p class="muted small">{{ $req->preferred_date?->translatedFormat('D, d M Y') }} · {{ $req->timeSlotLabel() }}</p>
                            </div>
                        </div>
                        <span class="badge badge-{{ $req->status }}">{{ $req->statusLabel() }}</span>
                    </div>
                    @include('livewire.partials.request-details', ['request' => $req, 'showContact' => true])
                    @php $job = $req->booking; @endphp
                    @if ($job?->status === 'confirmed')
                        <div style="display:flex;justify-content:flex-end;">
                            <button type="button" class="btn btn-primary btn-sm" wire:click="completeJob({{ $req->id }})" wire:confirm="{{ __('Mark this job as completed?') }}" wire:loading.attr="disabled">✓ {{ __('Mark job as completed') }}</button>
                        </div>
                    @elseif ($job?->status === 'completed')
                        @if ($job->rating)
                            <div class="rated">
                                <span class="stars">{{ str_repeat('★', $job->rating->rating) }}<span style="color:var(--line)">{{ str_repeat('★', 5 - $job->rating->rating) }}</span></span>
                                <span class="muted small">{{ __('Customer rating') }}</span>
                                @if ($job->rating->comment)<p>“{{ $job->rating->comment }}”</p>@endif
                            </div>
                        @else
                            <p class="muted small">{{ __('Completed :date · waiting for the customer’s rating.', ['date' => $job->completed_at?->translatedFormat('d M Y')]) }}</p>
                        @endif
                    @endif
                </div>
            @empty
                @if ($directRequests->isEmpty())
                    <div class="card empty">
                        <span class="icon-badge lg"><x-ui-icon name="calendar" /></span>
                        <p>{{ __('No jobs assigned to you yet. New jobs will appear here once our team assigns them or a customer books you.') }}</p>
                        <button type="button" class="btn btn-outline" wire:click="setTab('requests')">{{ __('See open requests') }}</button>
                    </div>
                @endif
            @endforelse
        </div>
    @endif

    {{-- Open requests in my service --}}
    @if ($activeTab === 'requests')
        <p class="muted" style="margin-bottom:18px;">{{ __('Open requests for your service. Interested? Call our team on :phone to take the job.', ['phone' => config('site.phone')]) }}</p>
        <div class="list">
            @forelse ($openRequests as $req)
                <div class="card req-card" wire:key="open-{{ $req->id }}">
                    <div class="req-top">
                        <div class="req-title">
                            <span class="icon-badge"><x-ui-icon :name="$req->category?->icon ?? 'home'" /></span>
                            <div>
                                <h3>{{ __($req->category?->name ?? 'Service') }} – {{ $req->city ?: __('City not given') }}</h3>
                                <p class="muted small">{{ __('Posted') }} {{ $req->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <span class="badge badge-open">{{ __('Open') }}</span>
                    </div>
                    @include('livewire.partials.request-details', ['request' => $req, 'showContact' => false])
                </div>
            @empty
                <div class="card empty">
                    <span class="icon-badge lg"><x-ui-icon name="clock" /></span>
                    <p>{{ __('No open requests for your service right now. Check back soon!') }}</p>
                </div>
            @endforelse
        </div>
    @endif

    {{-- Profile --}}
    @if ($activeTab === 'account')
        <form wire:submit="saveProfile" class="card card-pad-lg form" style="max-width:820px;">
            <div class="avatar-edit">
                @if ($avatar)
                    <img src="{{ $avatar->temporaryUrl() }}" class="avatar" alt="">
                @elseif ($currentAvatarUrl)
                    <img src="{{ str_starts_with($currentAvatarUrl, 'http') ? $currentAvatarUrl : asset('storage/' . $currentAvatarUrl) }}" class="avatar" alt="">
                @else
                    <span class="avatar">{{ mb_strtoupper(mb_substr($name, 0, 1)) }}</span>
                @endif
                <div class="field">
                    <label class="label" for="avatar">{{ __('Profile photo') }}</label>
                    <input id="avatar" type="file" wire:model="avatar" accept="image/*" class="small">
                    @error('avatar') <span class="error">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-row form-row-3">
                <div @class(['field', 'has-error' => $errors->has('name')])>
                    <label class="label" for="pr-name">{{ __('Full name') }}</label>
                    <input id="pr-name" type="text" wire:model="name" class="input">
                    @error('name') <span class="error">{{ $message }}</span> @enderror
                </div>
                <div @class(['field', 'has-error' => $errors->has('email')])>
                    <label class="label" for="pr-email">{{ __('Email') }}</label>
                    <input id="pr-email" type="email" wire:model="email" class="input">
                    @error('email') <span class="error">{{ $message }}</span> @enderror
                </div>
                <div @class(['field', 'has-error' => $errors->has('phone')])>
                    <label class="label" for="pr-phone">{{ __('Mobile number') }}</label>
                    <input id="pr-phone" type="tel" wire:model="phone" class="input">
                    @error('phone') <span class="error">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="form-row form-row-3">
                <div @class(['field', 'has-error' => $errors->has('service_category_id')])>
                    <label class="label" for="pr-cat">{{ __('Service') }}</label>
                    <select id="pr-cat" wire:model="service_category_id" class="select">
                        <option value="">{{ __('Select…') }}</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ __($category->name) }}</option>
                        @endforeach
                    </select>
                    @error('service_category_id') <span class="error">{{ $message }}</span> @enderror
                </div>
                <div @class(['field', 'has-error' => $errors->has('experience_years')])>
                    <label class="label" for="pr-exp">{{ __('Years of experience') }}</label>
                    <input id="pr-exp" type="number" min="0" wire:model="experience_years" class="input">
                    @error('experience_years') <span class="error">{{ $message }}</span> @enderror
                </div>
                <div @class(['field', 'has-error' => $errors->has('location')])>
                    <label class="label" for="pr-loc">{{ __('City / area') }}</label>
                    <input id="pr-loc" type="text" wire:model="location" class="input">
                    @error('location') <span class="error">{{ $message }}</span> @enderror
                </div>
            </div>
            <div @class(['field', 'has-error' => $errors->has('skills')])>
                <label class="label" for="pr-skills">{{ __('Skills') }}</label>
                <input id="pr-skills" type="text" wire:model="skills" class="input" placeholder="{{ __('Separate with commas') }}">
                @error('skills') <span class="error">{{ $message }}</span> @enderror
            </div>
            <div @class(['field', 'has-error' => $errors->has('bio')])>
                <label class="label" for="pr-bio">{{ __('Short introduction') }}</label>
                <textarea id="pr-bio" wire:model="bio" class="textarea"></textarea>
                @error('bio') <span class="error">{{ $message }}</span> @enderror
            </div>
            <div>
                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="saveProfile">
                    <span wire:loading wire:target="saveProfile" class="spinner"></span>
                    {{ __('Save profile') }}
                </button>
            </div>
        </form>
    @endif
</div>
