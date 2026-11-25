@php
    $me = auth()->user();
    $isPro = $me->isProvider();
    $bookingCount = \App\Models\ServiceRequest::where('user_id', $me->id)->count();
@endphp
<x-frontend-layout :title="__('Profile settings')">
    <section class="page-hero" style="padding-bottom:32px;">
        <div class="wrap">
            <nav class="crumbs">
                <a href="{{ route('home') }}">{{ __('Home') }}</a> ›
                <a href="{{ route('dashboard') }}">{{ __('My Account') }}</a> ›
                <span>{{ __('Profile settings') }}</span>
            </nav>
            <h1>{{ __('Profile settings') }}</h1>
            <p>{{ __('Update your account details and password.') }}</p>
        </div>
    </section>

    <section class="section section-soft" style="padding-top:32px;">
        <div class="wrap settings">
            <aside class="settings-side">
                <div class="card profile-card">
                    <div class="profile-avatar">{{ mb_strtoupper(mb_substr($me->name, 0, 1)) }}</div>
                    <h2>{{ $me->name }}</h2>
                    <p class="email">{{ $me->email }}</p>
                    <span class="badge {{ $isPro ? 'badge-primary' : 'badge-verified' }}">{{ $isPro ? __('Professional') : __('Customer') }}</span>

                    <dl class="profile-meta">
                        <div><dt>{{ __('Member since') }}</dt><dd>{{ $me->created_at?->translatedFormat('M Y') }}</dd></div>
                        <div><dt>{{ __('Mobile number') }}</dt><dd>{{ $me->phone ?: '—' }}</dd></div>
                        @unless ($isPro)
                            <div><dt>{{ __('Bookings') }}</dt><dd>{{ $bookingCount }}</dd></div>
                        @endunless
                    </dl>
                </div>

                <nav class="card side-links" aria-label="{{ __('My Account') }}">
                    <a href="{{ route('dashboard') }}"><x-ui-icon name="calendar" /> {{ $isPro ? __('My Jobs') : __('My Bookings') }}</a>
                    @if ($isPro)
                        <a href="{{ route('dashboard') }}"><x-ui-icon name="badge" /> {{ __('Professional profile') }}</a>
                    @else
                        <a href="{{ route('book') }}"><x-ui-icon name="sparkles" /> {{ __('Book a Service') }}</a>
                    @endif
                    <a href="{{ route('contact') }}"><x-ui-icon name="chat" /> {{ __('Help & support') }}</a>
                </nav>
            </aside>

            <div class="settings-main">
                <livewire:profile.update-profile-information-form />
                <livewire:profile.update-password-form />
                <livewire:profile.delete-user-form />
            </div>
        </div>
    </section>
</x-frontend-layout>
