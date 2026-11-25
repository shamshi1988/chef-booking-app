{{-- Layout for login, register and password pages: brand panel (left) + form card (right). --}}
@php
    $authServices = \App\Models\ServiceCategory::active()->take(8)->get(['name', 'icon']);
    $serviceCount = \App\Models\ServiceCategory::where('is_active', true)->count();
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name') }} - {{ __('Login') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <link rel="stylesheet" href="{{ asset('css/site.css') }}?v={{ filemtime(public_path('css/site.css')) }}">
        <script src="{{ asset('js/app.js') }}?v={{ filemtime(public_path('js/app.js')) }}" defer></script>
        @livewireStyles
        <style>
            /* Remaining Breeze pages (forgot / reset password, verify email) use these helpers */
            .bg-brand-primary { background-color: var(--primary); }
            .hover\:bg-brand-primary-dark:hover { background-color: var(--primary-dark); }
            .text-brand-primary { color: var(--primary); }
            .focus\:border-brand-primary:focus { border-color: var(--primary); }
        </style>
    </head>
    <body class="site">
        <div class="auth">
            {{-- Brand side --}}
            <aside class="auth-brand">
                <a href="{{ route('home') }}" aria-label="{{ config('app.name') }}"><x-application-logo size="38" accent="rgba(255,255,255,.18)" /></a>

                <div>
                    <h1>{{ __('Every home service,') }}<br><span>{{ __('one trusted place.') }}</span></h1>
                    <p class="auth-lead">{{ __('Book verified electricians, plumbers, AC technicians, physiotherapists, chefs and more – and track every booking from your account.') }}</p>

                    <div class="auth-features">
                        <div class="auth-feature"><span class="icon-badge sm"><x-ui-icon name="shield" /></span><div><strong>{{ __('Verified experts') }}</strong><span>{{ __('Background-checked and skill-tested professionals.') }}</span></div></div>
                        <div class="auth-feature"><span class="icon-badge sm"><x-ui-icon name="rupee" /></span><div><strong>{{ __('Pay after service') }}</strong><span>{{ __('No online payment needed to book.') }}</span></div></div>
                        <div class="auth-feature"><span class="icon-badge sm"><x-ui-icon name="clock" /></span><div><strong>{{ __('Your time slot') }}</strong><span>{{ __('Choose a date and time that suits you.') }}</span></div></div>
                        <div class="auth-feature"><span class="icon-badge sm"><x-ui-icon name="calendar" /></span><div><strong>{{ __('Track bookings') }}</strong><span>{{ __('See status and your assigned professional.') }}</span></div></div>
                    </div>

                    @if ($authServices->isNotEmpty())
                        <div class="auth-chips">
                            @foreach ($authServices as $service)
                                <span class="auth-chip"><x-ui-icon :name="$service->icon" /> {{ __($service->name) }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="auth-stats">
                    <div><strong>{{ $serviceCount }}+</strong><span>{{ __('Services') }}</span></div>
                    <div><strong>{{ count(\App\Models\ServiceArea::cities()) }}</strong><span>{{ __('Cities') }}</span></div>
                    <div><strong>7</strong><span>{{ __('Days a week') }}</span></div>
                    <div><strong>₹0</strong><span>{{ __('Booking fee') }}</span></div>
                </div>
            </aside>

            {{-- Form side --}}
            <main class="auth-main">
                <div class="auth-top">
                    <a href="{{ route('home') }}" class="auth-mobile-logo" aria-label="{{ config('app.name') }}"><x-application-logo size="30" /></a>
                    <a href="{{ route('home') }}" class="back">← {{ __('Back to website') }}</a>
                    <div class="lang-switch">
                        <a href="{{ route('lang.switch', 'en') }}" @class(['is-active' => app()->getLocale() === 'en'])>EN</a>
                        <a href="{{ route('lang.switch', 'hi') }}" @class(['is-active' => app()->getLocale() === 'hi'])>हि</a>
                        <a href="{{ route('lang.switch', 'bn') }}" @class(['is-active' => app()->getLocale() === 'bn'])>বাং</a>
                    </div>
                </div>

                <div class="auth-center">
                    <div class="auth-card">
                        {{ $slot }}
                    </div>
                </div>

                <p class="auth-foot">
                    {{ __('Need help?') }} <a class="link" href="tel:{{ preg_replace('/[^0-9+]/', '', config('site.phone')) }}">{{ config('site.phone') }}</a>
                    · <a class="link" href="{{ route('privacy-policy') }}">{{ __('Privacy policy') }}</a>
                </p>
            </main>
        </div>
        @livewireScripts
    </body>
</html>
