{{--
    Admin login: brand panel (left) + sign-in form (right).
    Styles are self-contained below (prefixed "al-") so they don't depend on Filament's compiled CSS.
--}}
@php
    $services = $this->getServiceList();
@endphp

<div class="al-page">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800|dm-serif-display:400,400i&display=swap" rel="stylesheet" />
    <style>
        .al-page, .al-page .fi-input, .al-page .fi-btn { font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif; }
        .al-page { position: fixed; inset: 0; z-index: 40; display: grid; grid-template-columns: 1fr; background: #fff; overflow-y: auto; }
        @media (min-width: 960px) { .al-page { grid-template-columns: 1.1fr 1fr; } }

        /* ---------- Brand side ---------- */
        .al-brand { display: none; position: relative; overflow: hidden; color: #fff; padding: 48px 56px; background: linear-gradient(145deg, #17233C 0%, #203152 50%, #2348E0 130%); }
        @media (min-width: 960px) { .al-brand { display: flex; flex-direction: column; justify-content: space-between; } }
        .al-brand::before, .al-brand::after { content: ''; position: absolute; border-radius: 50%; pointer-events: none; }
        .al-brand::before { width: 460px; height: 460px; right: -160px; top: -140px; background: radial-gradient(circle, rgba(255,255,255,.16), rgba(255,255,255,0) 70%); }
        .al-brand::after { width: 380px; height: 380px; left: -140px; bottom: -160px; background: radial-gradient(circle, rgba(49,92,255,.35), rgba(49,92,255,0) 70%); }
        .al-brand > * { position: relative; z-index: 1; }
        .al-brand-logo { color: #fff; }
        .al-brand h2 { font-family: 'DM Serif Display', Georgia, serif; font-size: clamp(32px, 3.2vw, 44px); line-height: 1.15; font-weight: 400; letter-spacing: -.02em; margin: 0 0 14px; color: #fff; }
        .al-brand h2 span { color: #8FA8FF; font-style: italic; }
        .al-lead { font-size: 16px; line-height: 1.6; color: rgba(255,255,255,.82); max-width: 460px; margin: 0; }
        .al-services { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; margin-top: 32px; max-width: 520px; }
        .al-service { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 12px; background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.14); font-size: 14px; font-weight: 600; backdrop-filter: blur(4px); }
        .al-service-icon { width: 32px; height: 32px; border-radius: 9px; background: rgba(255,255,255,.16); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .al-service-icon svg { width: 18px; height: 18px; }
        .al-features { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-top: 32px; max-width: 560px; }
        .al-feature strong { display: block; font-size: 14px; font-weight: 700; color: #fff; margin-top: 8px; }
        .al-feature span { font-size: 13px; color: rgba(255,255,255,.7); }
        .al-feature svg { width: 22px; height: 22px; color: #8FA8FF; }
        .al-brand-foot { font-size: 13px; color: rgba(255,255,255,.6); }

        /* ---------- Form side ---------- */
        .al-form-side { display: flex; align-items: center; justify-content: center; padding: 40px 20px; background: #f8fafc; min-height: 100vh; }
        .al-card { width: 100%; max-width: 420px; }
        .al-mobile-logo { display: flex; justify-content: center; margin-bottom: 28px; color: #0f172a; }
        @media (min-width: 960px) { .al-mobile-logo { display: none; } }
        .al-badge { display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; border-radius: 999px; background: rgba(49,92,255,.1); color: #315CFF; font-size: 12px; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; }
        .al-badge svg { width: 14px; height: 14px; }
        .al-title { font-family: 'DM Serif Display', Georgia, serif; font-size: 34px; font-weight: 400; letter-spacing: -.02em; color: #0f172a; margin: 16px 0 6px; }
        .al-sub { font-size: 15px; color: #64748b; margin: 0 0 28px; }
        .al-panel { background: #fff; border: 1px solid #e2e8f0; border-radius: 22px; padding: 28px; box-shadow: 0 1px 2px rgba(15,23,42,.05), 0 12px 32px rgba(15,23,42,.07); }
        .al-help { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-top: 22px; font-size: 14px; color: #64748b; }
        .al-help a { color: #315CFF; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
        .al-help a:hover { text-decoration: underline; }
        .al-secure { display: flex; align-items: center; justify-content: center; gap: 6px; margin-top: 26px; font-size: 13px; color: #94a3b8; }
        .al-secure svg { width: 15px; height: 15px; }

        /* Match Filament's form controls to the website style */
        .al-panel .fi-input-wrp { border-radius: 12px; }
        .al-panel .fi-input { padding-top: .7rem; padding-bottom: .7rem; }
        .al-panel .fi-btn { border-radius: 999px; padding-top: .8rem; padding-bottom: .8rem; font-weight: 600; font-size: 15px; box-shadow: 0 6px 16px rgba(49,92,255,.3); }
        .al-panel .fi-fo-field-label-content, .al-panel .fi-fo-field-wrp-label span { font-weight: 600; }

        /* Admin dark mode */
        .dark .al-form-side { background: #0b1220; }
        .dark .al-title, .dark .al-mobile-logo { color: #f8fafc; }
        .dark .al-panel { background: #111827; border-color: #1f2937; box-shadow: none; }
        .dark .al-badge { background: rgba(99,102,241,.18); color: #c7d2fe; }
    </style>

    {{-- Brand side --}}
    <aside class="al-brand">
        <div class="al-brand-logo"><x-application-logo size="40" accent="#315CFF" /></div>

        <div>
            <h2>{{ __('Every home service,') }}<br><span>{{ __('managed in one place.') }}</span></h2>
            <p class="al-lead">{{ __('Track booking requests, assign verified professionals, reply to enquiries and keep your service catalogue up to date.') }}</p>

            @if ($services->isNotEmpty())
                <div class="al-services">
                    @foreach ($services as $service)
                        <div class="al-service">
                            <span class="al-service-icon"><x-ui-icon :name="$service->icon" /></span>
                            {{ __($service->name) }}
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="al-features">
                <div class="al-feature"><x-ui-icon name="calendar" /><strong>{{ __('Bookings') }}</strong><span>{{ __('Assign & confirm') }}</span></div>
                <div class="al-feature"><x-ui-icon name="shield" /><strong>{{ __('Professionals') }}</strong><span>{{ __('Verify partners') }}</span></div>
                <div class="al-feature"><x-ui-icon name="chat" /><strong>{{ __('Enquiries') }}</strong><span>{{ __('Call back leads') }}</span></div>
            </div>
        </div>

        <div class="al-brand-foot">&copy; {{ date('Y') }} {{ config('app.name') }} · {{ config('site.phone') }}</div>
    </aside>

    {{-- Form side --}}
    <main class="al-form-side">
        <div class="al-card">
            <div class="al-mobile-logo"><x-application-logo size="40" accent="#315CFF" /></div>

            <span class="al-badge"><x-ui-icon name="shield" /> {{ __('Admin panel') }}</span>
            <h1 class="al-title">{{ __('Welcome back') }}</h1>
            <p class="al-sub">{{ __('Sign in to manage bookings, professionals and enquiries.') }}</p>

            <div class="al-panel">
                {{ $this->content }}
            </div>

            <div class="al-help">
                <a href="{{ route('home') }}"><span aria-hidden="true">←</span> {{ __('Back to website') }}</a>
                <span>{{ __('Need help?') }} <a href="mailto:{{ config('site.email') }}">{{ __('Contact') }}</a></span>
            </div>

            <div class="al-secure"><x-ui-icon name="shield" /> {{ __('Secure, encrypted connection') }}</div>
        </div>
    </main>
</div>
