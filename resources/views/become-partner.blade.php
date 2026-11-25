<x-frontend-layout :title="__('Become a Partner')">
    <section class="page-hero">
        <div class="wrap">
            <nav class="crumbs"><a href="{{ route('home') }}">{{ __('Home') }}</a> › <span>{{ __('Become a Partner') }}</span></nav>
            <h1>{{ __('Grow your business with us') }}</h1>
            <p>{{ __('Electrician, plumber, AC technician, physiotherapist, chef or any skilled professional – get regular jobs near you.') }}</p>
        </div>
    </section>

    <section class="section">
        <div class="wrap split">
            <div class="card card-pad-lg">
                <h2 style="font-size:24px;margin-bottom:24px;">{{ __('Partner registration') }}</h2>
                <livewire:provider-registration-form />
            </div>
            <aside class="sticky" style="display:grid;gap:20px;">
                <div class="card">
                    <h3 style="font-size:18px;margin-bottom:14px;">{{ __('Why partner with us?') }}</h3>
                    <ul class="ticks">
                        <li>{{ __('Steady flow of jobs in your area') }}</li>
                        <li>{{ __('Choose your own working hours') }}</li>
                        <li>{{ __('No joining fee') }}</li>
                        <li>{{ __('Build your reputation with customer ratings') }}</li>
                        <li>{{ __('Support from our team when you need it') }}</li>
                    </ul>
                </div>
                <div class="card">
                    <h3 style="font-size:18px;margin-bottom:10px;">{{ __('What happens next?') }}</h3>
                    <p class="muted small">{{ __('After you register, our team reviews your profile and may call you for a short verification. Once verified, you will start receiving booking requests in your dashboard.') }}</p>
                </div>
            </aside>
        </div>
    </section>
</x-frontend-layout>
