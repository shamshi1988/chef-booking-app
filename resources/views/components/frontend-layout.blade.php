@props(['title' => null, 'description' => null])
@php
    $siteName = config('app.name');
    $pageTitle = $title ? $title . ' | ' . $siteName : $siteName . ' – ' . __(config('site.tagline'));
    $navLinks = [
        ['route' => 'services.index', 'label' => __('Services'), 'active' => 'services.*'],
        ['route' => 'professionals', 'label' => __('Professionals'), 'active' => 'professionals'],
        ['route' => 'become-partner', 'label' => __('Become a Partner'), 'active' => 'become-partner'],
        ['route' => 'about', 'label' => __('About'), 'active' => 'about'],
        ['route' => 'contact', 'label' => __('Contact'), 'active' => 'contact'],
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $pageTitle }}</title>
        <meta name="description" content="{{ $description ?? __('Book trusted, verified professionals for electrical, plumbing, AC repair, physiotherapy, private chef, cleaning and more – all in one place.') }}">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <link rel="stylesheet" href="{{ asset('css/site.css') }}?v={{ filemtime(public_path('css/site.css')) }}">
        <script src="{{ asset('js/app.js') }}?v={{ filemtime(public_path('js/app.js')) }}" defer></script>
        @livewireStyles
    </head>
    <body class="site">
        <header class="site-header" data-header>
            <div class="wrap">
                <a href="{{ route('home') }}" class="brand" aria-label="{{ $siteName }} – {{ __('Home') }}">
                    <x-application-logo size="34" />
                </a>

                <nav class="nav" aria-label="{{ __('Main') }}">
                    @foreach ($navLinks as $link)
                        <a href="{{ route($link['route']) }}" @class(['is-active' => request()->routeIs($link['active'])])>{{ $link['label'] }}</a>
                    @endforeach
                </nav>

                <div class="header-actions">
                    <a href="{{ route('book') }}" class="btn btn-primary">{{ __('Book a Service') }}</a>
                    @auth
                        @php $me = auth()->user(); @endphp
                        <details class="acct" data-acct>
                            <summary aria-label="{{ __('My Account') }}">
                                <span class="acct-avatar">{{ mb_strtoupper(mb_substr($me->name, 0, 1)) }}</span>
                                <span class="acct-name">{{ \Illuminate\Support\Str::limit(\Illuminate\Support\Str::before($me->name, ' ') ?: $me->name, 12) }}</span>
                                <svg class="acct-caret" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
                            </summary>
                            <div class="acct-menu">
                                <div class="acct-head">
                                    <strong>{{ $me->name }}</strong>
                                    <span>{{ $me->email }}</span>
                                </div>
                                <a href="{{ route('dashboard') }}"><x-ui-icon name="calendar" /> {{ $me->isProvider() ? __('My Jobs') : __('My Bookings') }}</a>
                                <a href="{{ route('book') }}"><x-ui-icon name="sparkles" /> {{ __('Book a Service') }}</a>
                                <a href="{{ route('profile') }}"><x-ui-icon name="user" /> {{ __('Profile settings') }}</a>
                                <livewire:frontend-logout />
                            </div>
                        </details>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-light">{{ __('Login') }}</a>
                    @endauth
                    <div class="lang-switch">
                        <a href="{{ route('lang.switch', 'en') }}" @class(['is-active' => app()->getLocale() === 'en'])>EN</a>
                        <a href="{{ route('lang.switch', 'hi') }}" @class(['is-active' => app()->getLocale() === 'hi'])>हि</a>
                        <a href="{{ route('lang.switch', 'bn') }}" @class(['is-active' => app()->getLocale() === 'bn'])>বাং</a>
                    </div>
                </div>

                <button type="button" class="menu-toggle" data-menu-toggle aria-controls="mobile-menu" aria-expanded="false" aria-label="{{ __('Open menu') }}">
                    <x-ui-icon name="menu" />
                </button>
            </div>
        </header>

        <div id="mobile-menu" class="mobile-menu" hidden>
            @foreach ($navLinks as $link)
                <a href="{{ route($link['route']) }}" class="m-link">{{ $link['label'] }}</a>
            @endforeach
            <div class="m-actions">
                <a href="{{ route('book') }}" class="btn btn-primary btn-block">{{ __('Book a Service') }}</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-outline btn-block">{{ auth()->user()->isProvider() ? __('My Jobs') : __('My Bookings') }}</a>
                    <a href="{{ route('profile') }}" class="btn btn-light btn-block">{{ __('Profile settings') }}</a>
                    <livewire:frontend-logout-mobile />
                @else
                    <a href="{{ route('login') }}" class="btn btn-light btn-block">{{ __('Login') }}</a>
                @endauth
                <div class="lang-switch" style="justify-content:center;margin-top:8px;">
                    <a href="{{ route('lang.switch', 'en') }}" @class(['is-active' => app()->getLocale() === 'en'])>English</a>
                    <a href="{{ route('lang.switch', 'hi') }}" @class(['is-active' => app()->getLocale() === 'hi'])>हिन्दी</a>
                    <a href="{{ route('lang.switch', 'bn') }}" @class(['is-active' => app()->getLocale() === 'bn'])>বাংলা</a>
                </div>
            </div>
        </div>

        <main>
            {{ $slot }}
        </main>

        <footer class="site-footer">
            <div class="wrap">
                <div class="foot-grid">
                    <div class="foot-about">
                        <span style="color:#fff;"><x-application-logo size="32" /></span>
                        <p>{{ __('One place to book trusted professionals for every job at home – from repairs and maintenance to health and food.') }}</p>
                        <div class="foot-contact">
                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', config('site.phone')) }}">{{ config('site.phone') }}</a>
                            <a href="mailto:{{ config('site.email') }}">{{ config('site.email') }}</a>
                            <span>{{ __(config('site.hours')) }}</span>
                        </div>
                        <div class="socials">
                            <a href="{{ config('site.socials.facebook') }}" aria-label="Facebook"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M9 8H6v4h3v12h5V12h3.64l.36-4h-4V6.33C14 5.38 14.19 5 15.12 5H18V0h-3.81C10.6 0 9 1.58 9 4.62V8Z"/></svg></a>
                            <a href="{{ config('site.socials.instagram') }}" aria-label="Instagram"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor"/></svg></a>
                            <a href="{{ config('site.socials.youtube') }}" aria-label="YouTube"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M23.5 6.2a3 3 0 0 0-2.1-2.1C19.5 3.6 12 3.6 12 3.6s-7.5 0-9.4.5A3 3 0 0 0 .5 6.2 31 31 0 0 0 0 12a31 31 0 0 0 .5 5.8 3 3 0 0 0 2.1 2.1c1.9.5 9.4.5 9.4.5s7.5 0 9.4-.5a3 3 0 0 0 2.1-2.1A31 31 0 0 0 24 12a31 31 0 0 0-.5-5.8ZM9.6 15.6V8.4l6.2 3.6-6.2 3.6Z"/></svg></a>
                            <a href="https://wa.me/{{ config('site.whatsapp') }}" aria-label="WhatsApp"><x-ui-icon name="chat" /></a>
                        </div>
                    </div>
                    <div>
                        <h4>{{ __('Services') }}</h4>
                        <ul class="foot-links">
                            @foreach ($navServices->take(6) as $service)
                                <li><a href="{{ route('services.show', $service) }}">{{ __($service->name) }}</a></li>
                            @endforeach
                            <li><a href="{{ route('services.index') }}" style="color:#e2e8f0;">{{ __('All services') }} →</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4>{{ __('Company') }}</h4>
                        <ul class="foot-links">
                            <li><a href="{{ route('about') }}">{{ __('About us') }}</a></li>
                            <li><a href="{{ route('professionals') }}">{{ __('Our professionals') }}</a></li>
                            <li><a href="{{ route('become-partner') }}">{{ __('Become a partner') }}</a></li>
                            <li><a href="{{ route('contact') }}">{{ __('Contact us') }}</a></li>
                            <li><a href="{{ route('terms-conditions') }}">{{ __('Terms of service') }}</a></li>
                            <li><a href="{{ route('privacy-policy') }}">{{ __('Privacy policy') }}</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4>{{ __('Cities') }}</h4>
                        <ul class="foot-links">
                            @foreach (\App\Models\ServiceArea::cities() as $city)
                                <li><a href="{{ route('book', ['city' => $city]) }}">{{ __($city) }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="foot-bottom">
                    <span>&copy; {{ date('Y') }} {{ $siteName }}. {{ __('All rights reserved.') }}</span>
                    <span>{{ __(config('site.address')) }}</span>
                </div>
            </div>
        </footer>

        @livewireScripts
    </body>
</html>
